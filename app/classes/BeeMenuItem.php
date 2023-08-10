<?php 

final class BeeMenuItem
{
  private $item;
  private $text;
  private $url;
  private $icon;
  private $class;
  private $id;
  private $slug;
  private $subItems = [];

  function __construct()
  {
  }

  function setText($text)
  {
    $this->text = $text;  
  }

  function setUrl($url)
  {
    $this->url = $url;  
  }

  function setIcon($icon)
  {
    $this->icon = $icon;
  }

  function setClasses(array $classes)
  {
    $this->class = implode(' ', $classes);
  }

  function setId($id)
  {
    $this->id = $id;  
  }

  function setSlug($slug)
  {
    $this->slug = $slug;
  }

  function setSubItem(BeeMenuItem $item)
  {
    $this->subItems[] = $item->getItem();
  }

  private function process()
  {
    $this->item =
    [
      'text'    => $this->text,
      'id'      => $this->id,
      'class'   => $this->class,
      'slug'    => $this->slug,
      'icon'    => $this->icon, // para prevenir el sobre complicar las cosas
      'url'     => $this->url,
      'sub'     => $this->subItems
    ];
  }

  function getItem()
  {
    $this->process();
    return $this->item;
  }
}
