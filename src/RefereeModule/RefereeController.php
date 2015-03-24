<?php
namespace Cerad\Module\RefereeModule;

use Symfony\Component\HttpFoundation\Request;

class RefereeController
{
  public function mainAction(Request $request)
  {
    $id = $request->attributes->has('id') ? $request->attributes->get('id') : null;
    
    echo "RefereeController mainAction $id\n";
  }
}