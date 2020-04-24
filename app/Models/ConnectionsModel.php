<?php namespace App\Models;

use CodeIgniter\Model;

class ConnectionsModel extends Model{
  protected $table = 'connections';
  protected $allowedFields = ['c_user_id', 'c_resource_id', 'c_name'];
 

}
