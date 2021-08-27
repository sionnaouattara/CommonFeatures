<?php
/**
 * This file is part of the SmartStock project
 * Copyright (c) 2021 OAS
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace CommonFeatures\Factory\Repository;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Repository\AbstractRepositoryFactory;
use Doctrine\ODM\MongoDB\Repository\RepositoryFactory;
use CommonFeatures\Repository\BaseDocumentRepository;
use Doctrine\Persistence\ObjectRepository;

/**
 * Class CustomRepositoryFactory
 * @package CommonFeatures\Factory\Repository
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
class CustomRepositoryFactory extends AbstractRepositoryFactory implements RepositoryFactory
{

    /**
     * @param string $repositoryClassName
     * @param DocumentManager $documentManager
     * @param ClassMetadata $metadata
     * @return ObjectRepository|mixed
     */
    protected function instantiateRepository(
        $repositoryClassName,
        DocumentManager $documentManager,
        ClassMetadata $metadata
    ): ObjectRepository
    {
        if (class_exists($repositoryClassName) &&
            is_subclass_of($repositoryClassName, ObjectRepository::class)) {
            return new $repositoryClassName($documentManager, $documentManager->getUnitOfWork(), $metadata);
        }

        if (! is_subclass_of($repositoryClassName, BaseDocumentRepository::class)) {
            $repositoryClassName = BaseDocumentRepository::class;
        }
        return new $repositoryClassName($documentManager, $documentManager->getUnitOfWork(), $metadata);
    }
}
