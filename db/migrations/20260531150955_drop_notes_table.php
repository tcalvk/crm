<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DropNotesTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up(): void
    {
        if ($this->hasTable('notes')) {
            $this->table('notes')->drop()->save();
        }
    }
    
    public function down(): void
    {
        if (!$this->hasTable('notes')) {
            $this->table('notes')
                ->addColumn('user_id', 'integer', [
                    'null' => false,
                    'signed' => false,
                ])
                ->addColumn('title', 'string', [
                    'limit' => 255,
                    'null' => false,
                ])
                ->addColumn('body', 'text', [
                    'null' => true,
                ])
                ->addColumn('created_at', 'datetime', [
                    'default' => 'CURRENT_TIMESTAMP',
                    'null' => false,
                ])
                ->addIndex(['user_id'])
                ->create();
        }
    }
}
