<?php
namespace Cerad\Module\RefereeModule;

use Cerad\Component\HttpMessage\Request;
use Cerad\Component\HttpMessage\ResponseJson;

class RefereeController
{
  private $refereeRepository;
  
  public function __construct($refereeRepository)
  {
    $this->refereeRepository = $refereeRepository;
  }
  public function searchAction(Request $request)
  {
    $items = $this->refereeRepository->findAll();
    
    return new ResponseJson($items);
  }
  public function getOneAction(Request $request,$id)
  {
    $item = $this->refereeRepository->findOne($id);
    
    if ($item) return new ResponseJson($item);

    // Error problem
    return new ResponseJson(['error' => 'Item not found'],404);
  }
}