<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Type as EnhancedSelectionType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function count;
use function unserialize;

final class MigrateCommand extends Command
{
    private Connection $db;

    private string $typeIdentifier;

    private SymfonyStyle $io;

    public function __construct(Connection $db, EnhancedSelectionType $type)
    {
        $this->db = $db;
        $this->typeIdentifier = $type->getFieldTypeIdentifier();

        // Call to parent controller is mandatory in commands registered as services
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Migrates sckenhancedselection field type to version which stores content object data to database table.');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $statement = $this->getFields();
        $this->io->progressStart($statement->rowCount());

        while ($row = $statement->fetchAssociative()) {
            if ($row['data_text'] !== null) {
                $fieldId = (int) $row['id'];
                $version = (int) $row['version'];

                $this->removeSelectionDataForField($fieldId, $version);

                $identifiers = (array) unserialize($row['data_text']);
                if (count($identifiers) > 0) {
                    $this->createSelections($fieldId, $version, $identifiers);
                }

                $this->resetFieldData($fieldId, $version);
            }

            $this->io->progressAdvance();
        }

        $this->io->progressFinish();

        return 0;
    }

    private function getFields(): Result
    {
        $builder = $this->db->createQueryBuilder();
        $builder->select('cf.id', 'cf.version', 'cf.data_text')
            ->from('ibexa_content_field', 'cf')
            ->where(
                $builder->expr()->eq('cf.data_type_string', ':data_type_string')
            )
            ->setParameter('data_type_string', $this->typeIdentifier);

        return $builder->executeQuery();
    }

    private function resetFieldData(int $id, int $version): void
    {
        $builder = $this->db->createQueryBuilder();
        $builder->update('ibexa_content_field')
            ->set('data_text', 'null')
            ->where(
                $builder->expr()->eq('id', ':id')
            )->andWhere(
                $builder->expr()->eq('version', ':version')
            )
            ->setParameter('id', $id)
            ->setParameter('version', $version);

        $builder->executeStatement();
    }

    private function removeSelectionDataForField(int $id, int $version): void
    {
        $builder = $this->db->createQueryBuilder();
        $builder->delete($this->typeIdentifier)
            ->where(
                $builder->expr()->eq('contentobject_attribute_id', ':id')
            )->andWhere(
                $builder->expr()->eq('contentobject_attribute_version', ':version')
            )
            ->setParameter('id', $id)
            ->setParameter('version', $version);

        $builder->executeStatement();
    }

    private function createSelections(int $id, int $version, array $identifiers): void
    {
        $data = [
            'contentobject_attribute_id' => $id,
            'contentobject_attribute_version' => $version,
        ];

        foreach ($identifiers as $identifier) {
            $data['identifier'] = $identifier;
            $this->db->insert($this->typeIdentifier, $data);
        }
    }
}
