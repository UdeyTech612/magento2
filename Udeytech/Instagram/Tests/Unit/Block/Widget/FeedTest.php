<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Tests\Unit\Block\Widget;

use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Udeytech\Instagram\Block\Feed;
use Udeytech\Instagram\Helper\Data;
use Udeytech\Instagram\Model\Instagram;

/**
 * Class FeedTest
 * @package Udeytech\Instagram\Tests\Unit\Block\Widget
 */
class FeedTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     */
    const LIMIT_ITEMS = 4;

    /**
     *
     */
    const USER_ID = "udeytech";

    /**
     *
     */
    const HASH_TAG = "#udeytech";

    /**
     *
     */
    const PRODUCT_ID = 1;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Instagram|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mockModelInstagram;

    /**
     * @var Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mockDataHelper;

    /**
     * @var Registry|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mockRegistry;

    /**
     * @var Feed|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mockBlockFeed;

    /**
     * @dataProvider
     */
    public function testImagesHashTag()
    {
        $this->_mockBlockFeed->expects($this->any())
            ->method('getMode')
            ->willReturn($this->returnValue(Data::INSTAGRAM_MODE_BY_HASHTAG));
        $this->_mockBlockFeed->expects($this->any())
            ->method('getHashtag')
            ->willReturn($this->returnValue(self::HASH_TAG));
        $this->_mockBlockFeed->expects($this->any())
            ->method('getLimitItems')
            ->willReturn($this->returnValue(self::LIMIT_ITEMS));

        /*$this->_mockBlockFeed->expects($this->any())
            ->method('getImages')
            ->willReturn($this->returnSelf());*/


        $images = $this->_mockBlockFeed->getImages();

        var_dump($images);
    }

    /**
     *
     */
    protected function setUp()
    {
        $this->_objectManager = new ObjectManager($this);
        $this->_mockModelInstagram = $this->getMock('Udeytech\Instagram\Model\Instagram',
            ['getUserMedia', 'getTagMedia'], [], '', false, false);
        $this->_mockDataHelper = $this->getMock('Udeytech\Instagram\Helper\Data', [], [], '', false, false);
        $this->_mockRegistry = $this->getMock('Magento\Framework\Registry', [], [], '', false, false);

        $this->_mockBlockFeed = $this->getMock('Udeytech\Instagram\Block\Feed',
            ['getImages'],
            [
                'dataHelper' => $this->_mockDataHelper,
                'modelInstagram' => $this->_mockModelInstagram,
                'registry' => $this->_mockRegistry
            ], '', false, false
        );
    }

    /**
     *
     */
    protected function tearDown()
    {
        $this->_mockFeedBlock = null;
        $this->_objectManager = null;
        $this->_mockBlockFeed = null;
        $this->_mockRegistry = null;
    }


}
