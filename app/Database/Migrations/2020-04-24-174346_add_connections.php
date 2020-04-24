<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddConnections extends Migration
{
	public function up()
	{
			$this->forge->addField([
					'c_id'          => [
							'type'           => 'INT',
							'unsigned'       => TRUE,
							'auto_increment' => TRUE
					],
					'c_resource_id'          => [
						'type'           => 'INT',
					],
					'c_user_id'          => [
						'type'           => 'INT',
					],
					'c_name'       => [
							'type'           => 'VARCHAR',
							'constraint'     => '50',
					],
					
					]);
			$this->forge->addKey('c_id', TRUE);
			$this->forge->createTable('connections');
	}

	public function down()
	{
			$this->forge->dropTable('connections');
	}
}
