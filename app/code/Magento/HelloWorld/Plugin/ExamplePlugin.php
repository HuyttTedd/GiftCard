<?php
namespace Magento\HelloWorld\Plugin;


class ExamplePlugin
{

    public function beforeSetTitle(\Magento\HelloWorld\Controller\Index\Example $subject, $title)
    {

        //echo __METHOD__ . "</br>";
        //echo $title."ABC<br>";
        return '<br>00'.$title.'Mageplaza.com-1';
    }


    public function afterSetTitle(\Magento\HelloWorld\Controller\Index\Example $subject, $result)
    {
        //echo __METHOD__ . "</br>";
        return '<br>22'.$result.'Mageplaza.com-2';
    }
//
//
//    public function aroundSetTitle(\Magento\HelloWorld\Controller\Index\Example $subject, callable $proceed, $title)
//    {
//
//        $result = $proceed($title);
//         return $result;
//    }

}
