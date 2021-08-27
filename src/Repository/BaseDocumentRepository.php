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

namespace CommonFeatures\Repository;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * Class BaseDocumentRepository
 * @package CommonFeatures\Repository
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
class BaseDocumentRepository extends DocumentRepository
{
    /**
     * Count query row results after applied criteria
     *
     * @param array|null $criteria
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function countResult(array $criteria = null)
    {
        $qb = $this->createQueryBuilder();

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                if ($key !== '') {
                    $qb->field($key)->equals($value);
                }
            }
        }

        $qb->count();
        $qb->eagerCursor(true);
        $query = $qb->getQuery();

        return $query->execute();
    }

    /**
     * @param array|null $criteria
     * @param null|string $sort
     * @param int $dir
     * @param null|int $limit
     * @param null|int $offset
     * @param array $params
     * @return array
     * @throws \MongoException
     */
    public function fullSearchText(
        array $criteria = null,
        $sort = null,
        $dir = 1,
        $limit = null,
        $offset = null,
        $params = []
    ) {
        $qb = $this->createQueryBuilder();

        foreach ($criteria as $key => $value) {
            if ($key !== null) {
                $qb->addOr($qb->expr()->field($key)->equals(new \MongoRegex('/.*' . $value . '.*/i')));
            }
        }

        foreach ($params as $k => $val) {
            if ($k !== null) {
                $qb->field($k)->equals($val);
            }
        }

        if (null !== $sort) {
            $qb->sort($sort, $dir);
        }

        if ($limit !== null) {
            $qb->limit($limit)->skip($offset);
        }

        $cursor = $qb->getQuery()->toArray();

        $result = array();
        foreach ($cursor as $cur) {
            array_push($result, $cur);
        }

        return $result;
    }
}
