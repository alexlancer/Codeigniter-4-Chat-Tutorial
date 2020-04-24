<?php
namespace App\Controllers;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Libraries\Chat;



class Server extends BaseController
{
	public function index()
	{
		$server = IoServer::factory(
			new HttpServer(
				new WsServer(
					new Chat()
				)
			),
			8080
		);
	
		$db = db_connect();
		$builder  =$db->table('connections');
		$builder->where(['c_id >' => 0])->delete();

		$server->run();

	}

	//--------------------------------------------------------------------

}
