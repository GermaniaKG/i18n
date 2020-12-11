<?php
namespace tests;

use Germania\i18n\DGettextRenderer;

class DGettextRendererTest extends \PHPUnit\Framework\TestCase
{

    public function testInstantiation()
    {
        $sut = new DGettextRenderer("app");
        $sut->setFn( function($domain, $msgid) { return $msgid; } );

        $this->assertIsCallable( $sut );

        return $sut;
    }


    /**
     * @dataProvider provideExpansionVariables
     * @depends testInstantiation
     */
    public function testExpansion( $msgId, $variables, $expected, $sut )
    {
        $result = $sut($msgId, $variables);
        $this->assertEquals($result, $expected);
    }


    public function provideExpansionVariables()
    {
        return array(
            [ "Just a message", array(), "Just a message" ],
            [ "Just a {status} message", array('status' => 'success'), "Just a success message" ]
        );
    }
}


