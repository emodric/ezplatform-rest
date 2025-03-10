<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformRest\Tests\Server\Input\Parser;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeUpdateStruct;
use eZ\Publish\Core\Repository\ContentTypeService;
use EzSystems\EzPlatformRest\Server\Input\Parser\ContentTypeUpdate;

class ContentTypeUpdateTest extends BaseTest
{
    /**
     * Tests the ContentTypeUpdate parser.
     */
    public function testParse()
    {
        $inputArray = $this->getInputArray();

        $contentTypeUpdate = $this->getParser();
        $result = $contentTypeUpdate->parse($inputArray, $this->getParsingDispatcherMock());

        $this->assertInstanceOf(
            ContentTypeUpdateStruct::class,
            $result,
            'ContentTypeUpdateStruct not created correctly.'
        );

        $this->assertEquals(
            'updated_content_type',
            $result->identifier,
            'identifier not created correctly'
        );

        $this->assertEquals(
            'eng-US',
            $result->mainLanguageCode,
            'mainLanguageCode not created correctly'
        );

        $this->assertEquals(
            'remote123456',
            $result->remoteId,
            'remoteId not created correctly'
        );

        $this->assertEquals(
            '<title>',
            $result->urlAliasSchema,
            'urlAliasSchema not created correctly'
        );

        $this->assertEquals(
            '<title>',
            $result->nameSchema,
            'nameSchema not created correctly'
        );

        $this->assertTrue(
            $result->isContainer,
            'isContainer not created correctly'
        );

        $this->assertEquals(
            Location::SORT_FIELD_PATH,
            $result->defaultSortField,
            'defaultSortField not created correctly'
        );

        $this->assertEquals(
            Location::SORT_ORDER_ASC,
            $result->defaultSortOrder,
            'defaultSortOrder not created correctly'
        );

        $this->assertTrue(
            $result->defaultAlwaysAvailable,
            'defaultAlwaysAvailable not created correctly'
        );

        $this->assertEquals(
            ['eng-US' => 'Updated content type'],
            $result->names,
            'names not created correctly'
        );

        $this->assertEquals(
            ['eng-US' => 'Updated content type description'],
            $result->descriptions,
            'descriptions not created correctly'
        );

        $this->assertEquals(
            new \DateTime('2012-12-31T12:30:00'),
            $result->modificationDate,
            'creationDate not created correctly'
        );

        $this->assertEquals(
            14,
            $result->modifierId,
            'creatorId not created correctly'
        );
    }

    /**
     * Test ContentTypeUpdate parser throwing exception on invalid names.
     *
     * @expectedException \EzSystems\EzPlatformRest\Exceptions\Parser
     * @expectedExceptionMessage Invalid 'names' element for ContentTypeUpdate.
     */
    public function testParseExceptionOnInvalidNames()
    {
        $inputArray = $this->getInputArray();
        unset($inputArray['names']['value']);

        $contentTypeUpdate = $this->getParser();
        $contentTypeUpdate->parse($inputArray, $this->getParsingDispatcherMock());
    }

    /**
     * Test ContentTypeUpdate parser throwing exception on invalid descriptions.
     *
     * @expectedException \EzSystems\EzPlatformRest\Exceptions\Parser
     * @expectedExceptionMessage Invalid 'descriptions' element for ContentTypeUpdate.
     */
    public function testParseExceptionOnInvalidDescriptions()
    {
        $inputArray = $this->getInputArray();
        unset($inputArray['descriptions']['value']);

        $contentTypeUpdate = $this->getParser();
        $contentTypeUpdate->parse($inputArray, $this->getParsingDispatcherMock());
    }

    /**
     * Test ContentTypeUpdate parser throwing exception on invalid User.
     *
     * @expectedException \EzSystems\EzPlatformRest\Exceptions\Parser
     * @expectedExceptionMessage Missing '_href' attribute for User element in ContentTypeUpdate.
     */
    public function testParseExceptionOnInvalidUser()
    {
        $inputArray = $this->getInputArray();
        unset($inputArray['User']['_href']);

        $contentTypeUpdate = $this->getParser();
        $contentTypeUpdate->parse($inputArray, $this->getParsingDispatcherMock());
    }

    /**
     * Returns the ContentTypeUpdate parser.
     *
     * @return \EzSystems\EzPlatformRest\Server\Input\Parser\ContentTypeUpdate
     */
    protected function internalGetParser()
    {
        return new ContentTypeUpdate(
            $this->getContentTypeServiceMock(),
            $this->getParserTools()
        );
    }

    /**
     * Get the content type service mock object.
     *
     * @return \eZ\Publish\API\Repository\ContentTypeService
     */
    protected function getContentTypeServiceMock()
    {
        $contentTypeServiceMock = $this->createMock(ContentTypeService::class);

        $contentTypeServiceMock->expects($this->any())
            ->method('newContentTypeUpdateStruct')
            ->willReturn(new ContentTypeUpdateStruct());

        return $contentTypeServiceMock;
    }

    /**
     * Returns the array under test.
     *
     * @return array
     */
    protected function getInputArray()
    {
        return [
            'identifier' => 'updated_content_type',
            'mainLanguageCode' => 'eng-US',
            'remoteId' => 'remote123456',
            'urlAliasSchema' => '<title>',
            'nameSchema' => '<title>',
            'isContainer' => 'true',
            'defaultSortField' => 'PATH',
            'defaultSortOrder' => 'ASC',
            'defaultAlwaysAvailable' => 'true',
            'names' => [
                'value' => [
                    [
                        '_languageCode' => 'eng-US',
                        '#text' => 'Updated content type',
                    ],
                ],
            ],
            'descriptions' => [
                'value' => [
                    [
                        '_languageCode' => 'eng-US',
                        '#text' => 'Updated content type description',
                    ],
                ],
            ],
            'modificationDate' => '2012-12-31T12:30:00',
            'User' => [
                '_href' => '/user/users/14',
            ],
        ];
    }

    public function getParseHrefExpectationsMap()
    {
        return [
            ['/user/users/14', 'userId', 14],
        ];
    }
}
