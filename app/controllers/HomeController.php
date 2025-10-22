<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Campaign.php';
class HomeController {
  public function index() {
    $featured = Product::search('', null, null);
    $campaigns = Campaign::latest();
    render('home/index', ['featured'=>array_slice($featured,0,8), 'campaigns'=>$campaigns]);
  }
}