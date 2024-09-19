<?php

namespace Rapid\Laplus\Present\Generate;

use Rapid\Laplus\Present\Generate\Structure\MigrationFileListState;
use Rapid\Laplus\Present\Generate\Structure\MigrationListState;

class MigrationExporter
{
    use Concerns\ExportStubs;

    /**
     * @param MigrationGenerator[] $generators
     * @return MigrationFileListState
     */
    public function exportMigrationFiles(array $generators)
    {
        $generates = [];
        foreach ($generators as $tag => $generator)
        {
            $generates[$tag] = $generator->generate();
        }

        return $this->exportMigrationFilesFrom($generates);
    }

    /**
     * Export migration files from MigrationListState object
     *
     * @param MigrationListState[] $migrationsAll
     * @return MigrationFileListState
     */
    protected function exportMigrationFilesFrom(array $migrationsAll)
    {
        $files = new MigrationFileListState();
        $createdTables = isset($this->definedMigrationState) ? array_keys($this->definedMigrationState->tables) : [];

        $dateIndex = time();

        // Export normals
        foreach ($migrationsAll as $tag => $migrations)
        {
            foreach ($migrations->all as $migration)
            {
                if ($migration->isLazy) continue;

                $name = date('Y_m_d_His', $dateIndex++) . '_' . $migration->getBestFileName();

                switch ($migration->command)
                {
                    case 'table':
                        $tableName = $migration->table;
                        $isCreating = !in_array($tableName, $createdTables);
                        if ($isCreating)
                            $createdTables[] = $tableName;
                        $files->files[$name] = $isCreating ?
                            $this->makeMigrationCreate($migration) :
                            $this->makeMigrationTable($migration);
                        $files->files[$name]->tag = $tag;
                        break;

                    case 'drop':
                        $files->files[$name] = $this->makeMigrationDrop($migration);
                        $files->files[$name]->tag = $tag;
                        break;

                    default:
                        // die("Error"); // TODO : Should change
                }
            }
        }

        // Export lazies
        foreach ($migrationsAll as $tag => $migrations)
        {
            foreach ($migrations->all as $migration)
            {
                if (!$migration->isLazy) continue;

                $name = date('Y_m_d_His', $dateIndex++) . '_' . $migration->getBestFileName();

                switch ($migration->command)
                {
                    case 'table':
                        $tableName = $migration->table;
                        $isCreating = !in_array($tableName, $createdTables);
                        if ($isCreating)
                            $createdTables[] = $tableName;
                        $files->files[$name] = $isCreating ?
                            $this->makeMigrationCreate($migration) :
                            $this->makeMigrationTable($migration);
                        $files->files[$name]->tag = $tag;
                        break;

                    case 'drop':
                        $files->files[$name] = $this->makeMigrationDrop($migration);
                        $files->files[$name]->tag = $tag;
                        break;

                    default:
                        // die("Error"); // TODO : Should change
                }
            }
        }

        return $files;
    }

    /**
     * Export migration stubs
     *
     * @param MigrationFileListState $files
     * @return array<string, string>
     */
    public function exportMigrationStubs(MigrationFileListState $files)
    {
        $stub = file_get_contents(__DIR__ . '/../../Commands/stubs/migration.stub');

        $result = [];
        foreach ($files->files as $name => $file)
        {
            $up = implode("\n        ", $file->up);
            $down = implode("\n        ", $file->down);

            $result[$name] = str_replace(
                ['{{ up }}', '{{ down }}'],
                [$up, $down],
                $stub,
            );
        }

        return $result;
    }

}