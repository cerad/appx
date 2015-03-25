<?php
namespace Cerad\Module\RefereeModule;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class RefereeController
{
  private $refereeRepository;
  
  public function __construct($refereeRepository)
  {
    $this->refereeRepository = $refereeRepository;
  }
  public function mainAction(Request $request)
  {
    $id = $request->attributes->has('id') ? $request->attributes->get('id') : null;
    
    switch($request->getMethod())
    {
      case 'GET':
      case 'OPTIONS':
        return $id ? $this->getOneAction($request,$id) : $this->searchAction($request);
    }
  //echo "RefereeController mainAction $id {$request->getMethod()}\n";
  }
  public function searchAction($request)
  {
    $items = $this->refereeRepository->findAll();
    
    return new JsonResponse($items);
  }
  public function getOneAction($request,$id)
  {
    $item = $this->refereeRepository->findOne($id);
    
    if ($item) return new JsonResponse($item);
    
    // Error problem
    return new JsonResponse(['error' => 'Item not found'],404);
  }
}