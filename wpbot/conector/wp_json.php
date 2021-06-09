<?php
namespace wpbot\conector;

use wpbot\manager\json;

class wp_json
{
    public $url;
    
    public function __construct($url)
    {
        $this->url = $url;
        $this->json_manager = new json();
    }
    public function getCategories($page = 1, $count = 10)
    {
        return $this->json_manager->decode($this->url.'/wp-json/wp/v2/categories?page='.$page.'&per_page='.$count);
    }
    public function getPosts($category, $page)
    {
        return $this->json_manager->decode($this->url.'/wp-json/wp/v2/posts?categories='.$category.'&page='.$page.'&per_page=1');
    }
}
