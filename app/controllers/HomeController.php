<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Campaign.php';
class HomeController {
  public function index() {
    $featured = Product::getFeatured(8);
    $campaigns = Campaign::latest();
    render('home/index', ['featured'=>$featured, 'campaigns'=>$campaigns]);
  }
}