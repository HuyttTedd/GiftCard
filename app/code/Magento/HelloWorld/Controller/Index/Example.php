<?php
namespace Magento\HelloWorld\Controller\Index;

class Example extends \Magento\Framework\App\Action\Action
{
    protected $title;

    public function execute()
    {
        echo "Start<br>";
        echo $this->setTitle(555)."ABCD";
        echo "<br>END";
    }


    public function setTitle($title)
    {
        //echo $title."EFF<br>";
        echo __METHOD__."<br>";
         //return $this->title = $title;
        return 666;
    }

    public function getTitle()
    {
        echo __METHOD__."<br>";
        return $this->title;
    }
}
