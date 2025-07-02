<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class AppController extends Controller
{
  protected $request;

  public function __construct(Request $request)
  {
    $this->middleware('auth');

    $this->request = $request;

    config(['site.page.title' => 'Alisha Back Office']);
  }
}