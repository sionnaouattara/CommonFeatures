<?php
/**
 * This file is part of the SmartStock project
 * Copyright (c) 2020 OAS
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace CommonFeatures\Service;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\Persistence\ObjectManager;
use JMS\Serializer\SerializationContext;
use CommonFeatures\EventManager\EventManagerAwareTrait;
use CommonFeatures\EventManager\TriggerEventTrait;
use CommonFeatures\Model\ModelInterface;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Log\Logger;
use Laminas\Log\Processor\PsrPlaceholder;
use Laminas\Log\Writer\Stream;

/**
 * Class AbstractService
 *
 * @package CommonFeatures\Service
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
abstract class AbstractService implements AbstractServiceInterface
{
    use EventManagerAwareTrait;
    use TriggerEventTrait;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var DoctrineObject
     */
    protected $hydrator;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var array
     */
    protected $serializerFormat = ['json', 'xml', 'yml'];

    /**
     * @var string
     */
    protected $logEntryEntity = 'Gedmo\\Loggable\\Entity\\LogEntry';

    /**
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * AbstractService constructor.
     * @param null $entityName
     * @param ObjectManager $objectManager
     */
    public function __construct($entityName = null, ObjectManager $objectManager)
    {
        if (! is_null($entityName)) {
            $this->setEntity($entityName);
        }

        $this->objectManager = $objectManager;
        $this->enableSoftDeleteableFilter(true);
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param string $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->hydrator = new DoctrineObject($this->objectManager);
        }
        return $this->hydrator;
    }

    /**
     * @param HydratorInterface $hydrator
     * @return $this
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
        return $this;
    }

    /**
     * Uses the hydrator to convert the entity to an array.
     *
     * Use this method to ensure that you're working with an array.
     *
     * @param object $entity
     * @param HydratorInterface|null $hydrator
     * @return array
     */
    public function toArray($entity, HydratorInterface $hydrator = null)
    {
        if (is_array($entity)) {
            return $entity; // cut down on duplicate code
        } elseif (is_object($entity)) {
            if (! $hydrator) {
                $hydrator = $this->getHydrator();
            }
            return $hydrator->extract($entity);
        }
        throw new Exception\InvalidArgumentException('Entity passed to db mapper should be an array or object.');
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        if (! $this->serializer) {
            $this->setSerializer();
        }
        return $this->serializer;
    }

    /**
     * @param mixed $serializer
     * @return $this
     */
    public function setSerializer($serializer = null)
    {
        if (! $serializer) {
            $serializer = \JMS\Serializer\SerializerBuilder::create()
                ->setPropertyNamingStrategy(
                    new \JMS\Serializer\Naming\SerializedNameAnnotationStrategy(
                        new \JMS\Serializer\Naming\IdenticalPropertyNamingStrategy()
                    )
                )
                ->setCacheDir(getcwd() . '/data/JMSSerializer')
                ->build();
        }
        $this->serializer = $serializer;
        return $this;
    }

    /**
     * @param array|ModelInterface $entity
     * @param string $format
     * @param null $groups
     * @return mixed|string
     * @throws \Exception
     */
    public function serialize($entity, $format = 'json', $groups = null)
    {
        if (! in_array($format, $this->serializerFormat)) {
            throw new Exception\InvalidArgumentException('Format ' . $format . ' is not valid');
        }
        $serializer = $this->getSerializer();

        if (! $serializer instanceof SerializerInterface) {
            throw new \Exception('Serializer service must be instance of ' .
                get_class(SerializerInterface::class));
        }

        $context = SerializationContext::create()->enableMaxDepthChecks();
        $groups = (array) $groups;
        if (count($groups)) {
            $context->setGroups($groups);
        }
        $serialize  = $serializer->serialize($entity, $format, $context);

        if ($format === 'json') {
            $serialize = json_decode($serialize);
        }
        return $serialize;
    }

    /**
     * @param array $data
     * @param ModelInterface|null $entity
     * @return ModelInterface|mixed|null
     * @throws \Exception
     */
    public function hydrate($data, $entity = null)
    {
        if (is_null($entity)) {
            $entity = $this->createEntity();
        }

        # Gives the possibility to change $argv in listeners
        $argv = ['data' => &$data, 'entity' => $entity];
        $this->triggerEvent(__FUNCTION__.'.pre', $argv);
        extract($argv);

        try {
            $entity = $this->getHydrator()->hydrate($data, $entity);
        } catch (\Exception $ex) {
            $hydrator = $this->objectManager->getHydratorFactory();
            $hydrator->hydrate($entity, $data);
        }

        $this->triggerEvent(__FUNCTION__.'.post', $argv);

        return $entity;
    }

    /**
     * Creates a new instance of the given entityName or of the already known
     * one whose FQDN is stored in the className property.
     *
     * @param string $entityName
     * @return ModelInterface|mixed
     * @throws \Exception
     */
    public function createEntity($entityName = null)
    {
        if (null === $entityName) {
            $entityName = $this->getEntity();
            if (!$entityName) {
                throw new Exception\InvalidArgumentException("entityName not set. Can't create class.");
            }
        } elseif (false === class_exists($entityName)) {
            throw new Exception\InvalidArgumentException(
                "'".$entityName."' class doesn't exist. Can't create class."
            );
        }

        return new $entityName;
    }

    /**
     * @return \Doctrine\Persistence\ObjectRepository
     */
    public function getRepository()
    {
        $class = $this->getEntityClassName();
        return $this->objectManager->getRepository($class);
    }

    /**
     * Get Entity Reference
     *
     * @param string|int $id
     * @param string|null $class
     * @return mixed
     */
    public function getReference($id, $class = null)
    {
        if (null === $class) {
            $class = $this->getEntityClassName();
        }
        return $this->objectManager->getReference($class, $id);
    }

    /**
     * @param null $class
     * @return \Doctrine\Persistence\Mapping\ClassMetadata
     */
    public function getEntityClassMetadata($class = null)
    {
        if (null === $class) {
            $class = $this->getEntityClassName();
        }
        return $this->objectManager->getClassMetadata($class);
    }

    /**
     * Return log entries
     * From Loggable behavioral extension for Gedmo
     *
     * @param ModelInterface $entity
     * @return mixed
     */
    public function getLogEntries(ModelInterface $entity)
    {
        $logEntryEntity = $this->objectManager->getRepository($this->logEntryEntity);
        return $logEntryEntity->getLogEntries($entity);
    }

    /**
     * @param string $id
     * @return ModelInterface|mixed
     *
     * @triggers find.pre
     * @triggers find.post
     * @triggers find
     */
    public function find($id)
    {
        # Gives the possibility to change $argv in listeners
        $argv = ['id' => &$id];
        $this->triggerEvent(__FUNCTION__.'.pre', $argv);
        extract($argv);

        $entity = $this->getRepository()->find($id);

        $this->triggerEvent(__FUNCTION__, ['entity' => $entity]);
        $this->triggerEvent(__FUNCTION__.'.post', ['id' => $id, 'entity' => $entity]);

        return $entity;
    }

    /**
     * @param array $criteria
     * @return ModelInterface|mixed
     *
     * @triggers findOneBy.pre
     * @triggers findOneBy.post
     * @triggers find
     */
    public function findOneBy(array $criteria)
    {
        # Gives the possibility to change $argv in listeners
        $argv = ['criteria' => &$criteria];
        $this->triggerEvent(__FUNCTION__ .'.pre', $argv);
        extract($argv);

        $entity = $this->getRepository()->findOneBy($criteria);

        $this->triggerEvent('find', ['entity' => $entity]);
        $this->triggerEvent(__FUNCTION__.'.post', ['criteria' => $criteria, 'entity' => $entity]);

        return $entity;
    }

    /**
     * @param array|string $orderBy
     * @return array
     *
     * @triggers findAll.pre
     * @triggers findAll.post
     * @triggers find
     */
    public function findAll($orderBy = null)
    {
        if (is_string($orderBy)) {
            $orderBy = [$orderBy => 'asc'];
        }

        # Gives the possibility to change $argv in listeners
        $argv = ['orderBy' => &$orderBy];
        $this->triggerEvent(__FUNCTION__.'.pre', $argv);
        extract($argv);

        $entities = $this->getRepository()->findBy(array(), $orderBy);

        /*foreach ($entities as $entity) {
            $this->triggerEvent('find', ['entity' => $entity]);
        }*/

        $this->triggerEvent(__FUNCTION__.'.post', ['orderBy' => $orderBy, 'entities' => $entities]);

        return $entities;
    }

    /**
     * @param array $criteria
     * @param array|string $orderBy
     * @param int $limit
     * @param int $offset
     * @return array
     *
     * @triggers findBy.pre
     * @triggers findBy.post
     * @triggers find
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        if (is_string($orderBy)) {
            $orderBy = [$orderBy => 'asc'];
        }

        # Gives the possibility to change $argv in listeners
        $argv = ['criteria' => &$criteria, 'orderBy' => &$orderBy, 'limit' => &$limit, 'offset' => &$offset];
        $this->triggerEvent(__FUNCTION__.'.pre', $argv);
        extract($argv);

        $entities = $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);

        /*foreach ($entities as $entity) {
            $this->triggerEvent('find', ['entity' => $entity]);
        }*/

        $this->triggerEvent(__FUNCTION__ . '.post', array_merge($argv, ['entities' => $entities]));

        return $entities;
    }

    /**
     * @param array|ModelInterface $entity
     * @param bool $flush
     * @param null|string $event
     * @return array|ModelInterface|mixed|null
     * @throws \Exception
     */
    public function save($entity, $flush = true, $event = null)
    {
        # Gives the possibility to change $argv in listeners
        $argv = ['entity' => &$entity, 'flush' => &$flush];
        $this->triggerEvent(__FUNCTION__ . '.pre', $argv);
        extract($argv);

        if (is_array($entity)) {
            # Means we only have an array of data here
            $data = $entity;
            $entity = null;
            if (array_key_exists('id', $data) && ! empty($data['id'])) {
                # We have an id here > it's an update !
                $entity = $this->find($data['id']);
                if ($entity) {
                    unset($data['id']);
                }
            }
            $entity = $this->hydrate($data, $entity);
        }

        $this->objectManager->persist($entity);

        if ($flush === true) {
            $this->objectManager->flush();
        }

        if (null !== $event && $event !== __FUNCTION__) {
            $this->triggerEvent($event, array_merge($argv, ['saved' => $entity]));
        }
        $this->triggerEvent(__FUNCTION__.'.post', array_merge($argv, ['saved' => $entity]));

        return $entity;
    }

    /**
     * @param string|array|ModelInterface $entity
     * @param bool $flush
     * @return ModelInterface
     */
    public function delete($entity, $flush = true)
    {
        # Gives the possibility to change $argv in listeners
        $argv = ['entity' => &$entity, 'flush' => &$flush];
        $this->triggerEvent(__FUNCTION__.'.pre', $argv);
        extract($argv);

        if (is_string($entity)) {
            # Means we only have the id of the entity
            $entity = $this->find($entity);
        } elseif (is_array($entity)) {
            # Means we only have criteria precise enough to get the entity
            $entity = $this->findOneBy($entity);
        }

        $this->objectManager->remove($entity);

        if ($flush === true) {
            $this->objectManager->flush();
        }

        $this->triggerEvent(__FUNCTION__.'.post', array_merge($argv, ['deleted' => $entity]));

        return $entity;
    }

    /**
     * enable/disable entityManager softDeleteable
     * @param boolean $enable
     * @return $this
     */
    public function enableSoftDeleteableFilter($enable = true)
    {
        if (method_exists($this->objectManager, 'getFilterCollection')) {
            $filters = $this->objectManager->getFilterCollection();
        } else {
            $filters = $this->objectManager->getFilters();
        }

        if (true === $enable) {
            $filters->enable('softDeleteable');
        } else {
            $filters->disable('softDeleteable');
        }
        return $this;
    }

    /**
     * @param array $filters
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function filters(array $filters, array $orderBy = null, $limit = null, $offset = null)
    {
        $searchParam = isset($filters['q']) ? $filters['q'] : '';
        unset($filters['q']);
        if ($searchParam === '') {
            return $this->getMatchingRecords($filters, $orderBy, $limit, $offset);
        }

        if (! method_exists($this->getEntity(), 'getClassFields')) {
            return $this->getMatchingRecords($filters, $orderBy, $limit, $offset);
        }

        if (! method_exists($this->getRepository(), 'fullSearchText')) {
            return $this->getMatchingRecords($filters, $orderBy, $limit, $offset);
        }

        return $this->search($searchParam, $filters, $orderBy, $limit, $offset);
    }


    /**
     * @param string $searchTerm
     * @param array $filters
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function search(
        $searchTerm,
        array $filters = [],
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        $entity = $this->getEntity();
        $fields = $entity->getClassFields();
        $criteria = [];
        foreach ($fields as $field) {
            $criteria[$field] = $searchTerm;
        }

        $sort = null;
        $dir = 1;
        if (is_array($orderBy) && count($orderBy)) {
            foreach ($orderBy as $k => $v) {
                $sort = $k;
                $dir = $v;
            }
        }

        return $this->getRepository()->fullSearchText(
            $criteria,
            $sort,
            $dir,
            $limit,
            $offset,
            $filters
        );
    }

    /**
     * @param array $filters
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return mixed
     * @throws \Exception
     */
    protected function getMatchingRecords(array $filters, array $orderBy = null, $limit = null, $offset = null)
    {
        $criteria = $this->buildCriteria($filters);

        if (null !== $orderBy && count($orderBy)) {
            $criteria->orderBy($orderBy);
        }

        if (null !== $limit) {
            $criteria->setMaxResults(intval($limit));
        }

        if (null !== $offset) {
            $criteria->setFirstResult(intval($offset));
        }

        return $this->getRepository()->matching($criteria);
    }

    /**
     * @param array $filters
     * @return int
     * @throws \Exception
     */
    public function countMatchingRecords($filters)
    {
        $matches = $this->filters($filters);
        return count($matches);
    }

    /**
     * @param array $filters
     * @return Criteria
     * @throws \Exception
     */
    protected function buildCriteria(array $filters)
    {
        $entity = $this->hydrate($filters);

        $expr = Criteria::expr();
        $criteria = Criteria::create();

        foreach ($filters as $key => $value) {
            $method = 'get' . ucfirst($key);
            $criteria->andWhere($expr->eq($key, $entity->{$method}()));
        }

        return $criteria;
    }

    /**
     * @return string
     */
    private function getEntityClassName()
    {
        if (is_object($this->getEntity())) {
            $class = get_class($this->getEntity());
        } else {
            $class = $this->getEntity();
        }
        return $class;
    }

    /**
     * @param mixed $logger
     * @return $this
     */
    public function setLogger($logger = null)
    {
        if (! $logger) {
            $writer = new Stream([
                'stream' => getcwd() . '/data/log/application.log',
            ]);
            $logger = new Logger();
            $logger->addWriter($writer);
            $logger->addProcessor(new PsrPlaceholder);
        }
        $this->logger = $logger;
        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        if (!$this->logger) {
            $this->setLogger();
        }
        return $this->logger;
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }
}
