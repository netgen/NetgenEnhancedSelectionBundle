<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Command;

use PDO;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOStatement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Type as EnhancedSelectionType;

class MigrateCommand extends Command
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    /**
     * @var string
     */
    protected $typeIdentifier;

    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    protected $io;

    public function __construct(Connection $db, EnhancedSelectionType $type)
    {
        $this->db = $db;
        $this->typeIdentifier = $type->getFieldTypeIdentifier();

        // Call to parent controller is mandatory is commands registered as services
        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('enhanced-selection:migrate')
            ->setDescription('Migrates sckenhancedselection field type to version which stores content object data to database table.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $statement = $this->getFields();
        $this->io->progressStart($statement->rowCount());

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            if ($row['data_text'] !== null) {
                $this->removeSelectionDataForField($row['id'], $row['version']);

                $identifiers = (array) unserialize($row['data_text']);
                if (count($identifiers) > 0) {
                    $this->createSelections($row['id'], $row['version'], $identifiers);
                }

                $this->resetFieldData($row['id'], $row['version']);
            }

            $this->io->progressAdvance();
        }

        $this->io->progressFinish();

        return 0;
    }

    protected function getFields()
    {
        $builder = $this->db->createQueryBuilder();
        $builder->select('a.id', 'a.version', 'a.data_text')
            ->from('ezcontentobject_attribute', 'a')
            ->where(
                $builder->expr()->eq('a.data_type_string', ':data_type_string')
            )
            ->setParameter('data_type_string', $this->typeIdentifier);

        return $builder->execute();
    }

    protected function resetFieldData($id, $version)
    {
        $builder = $this->db->createQueryBuilder();
        $builder->update('ezcontentobject_attribute')
            ->set('data_text', 'null')
            ->where(
                $builder->expr()->eq('id', ':id')
            )->andWhere(
                $builder->expr()->eq('version', ':version')
            )
            ->setParameter('id', $id)
            ->setParameter('version', $version);

        $builder->execute();
    }

    protected function removeSelectionDataForField($id, $version)
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

        $builder->execute();
    }

    protected function createSelections($id, $version, array $identifiers)
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
