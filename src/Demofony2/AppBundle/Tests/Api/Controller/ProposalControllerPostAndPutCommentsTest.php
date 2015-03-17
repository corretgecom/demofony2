<?php

namespace Demofony2\AppBundle\Tests\Api\Controller;

use Liip\FunctionalTestBundle\Annotations\QueryCount;

class ProposalControllerPostAndPutCommentsTest extends AbstractDemofony2ControllerTest
{
    const PROPOSAL_ID = 1;

    const USER1 = 'user1';
    const USER_PASSWORD1 = 'user1';

    const USER2 = 'user2';
    const USER_PASSWORD2 = 'user2';

    /**
     * test create comment
     * test edit comment
     * test comment not belongs to process participation
     * test user is not owner
     *
     * @QueryCount(100)
     */
    public function testInDebatePeriodLogged()
    {
        //test not logged
        $response = $this->request($this->getValidParameters());
//        var_dump($this->client->getResponse());
        $this->assertStatusResponse(401);

        //login user1
        $this->initialize(self::USER1, self::USER_PASSWORD1);

        //test in closed period
        $url = $this->getDemofony2Url(2);
        $response = $this->request($this->getValidParameters(), $url);
        $this->assertStatusResponse(400);

        //post a comment
        $url = $this->getDemofony2Url(1);
        $response = $this->request($this->getValidParameters(), $url);
        $this->assertStatusResponse(201);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('author', $response);
        $this->assertEquals(self::USER1, $response['author']['username']);

        $commentId = $response['id'];

        //test edit
        $url = $this->getEditUrl(1, $commentId);
        $response = $this->request($this->getValidParameters(), $url, 'PUT');
        $this->assertStatusResponse(204);

        //test comment not belongs to process particiaption
        $url = $this->getEditUrl(2, $commentId);
        $response = $this->request($this->getValidParameters(), $url, 'PUT');
        $this->assertStatusResponse(400);

        //login user2
        $this->initialize(self::USER2, self::USER_PASSWORD2);

        //test user not owner
        $url = $this->getEditUrl(1, $commentId);
        $response = $this->request($this->getValidParameters(), $url, 'PUT');
        $this->assertStatusResponse(403);

        //post child comment
        $params = array(
            'title' => 'test',
            'comment' => 'test',
            'parent' => $commentId,
        );
        $url = $this->getDemofony2Url(1);
        $response = $this->request($params, $url);
        $this->assertStatusResponse(201);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('author', $response);
        $this->assertEquals(self::USER2, $response['author']['username']);

//        test children count
        $url = $this->getDemofony2Url(1);
        $response = $this->request([], $url, 'GET');
        $this->assertEquals(1, $response['comments'][0]['children_count']);

        //test when comments are moderated
        //post child comment
        $url = $this->getDemofony2Url(3);
        $response = $this->request($this->getValidParameters(), $url);
        $this->assertStatusResponse(201);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('author', $response);
        $this->assertEquals(self::USER2, $response['author']['username']);

//        test children count
        $url = $this->getDemofony2Url(3);
        $response = $this->request([], $url, 'GET');
        $this->assertCount(0, $response['comments']);

        //repond one comment ok
        $params = array(
            'title' => 'test2',
            'comment' => 'test2',
            'parent' => $commentId,
        );

        $this->initialize(self::USER1, self::USER_PASSWORD1);
        $url = $this->getDemofony2Url(1);
        $response = $this->request($params, $url);
        $this->assertStatusResponse(201);

        //500 because only one level respond
        $params = array(
            'title' => 'test2',
            'comment' => 'test2',
            'parent' => $response['id'],
        );

        $this->initialize(self::USER1, self::USER_PASSWORD1);
        $url = $this->getDemofony2Url(1);
        $response = $this->request($params, $url);
        $this->assertStatusResponse(500);
    }

    public function getMethod()
    {
        return 'POST';
    }

    public function getDemofony2Url($ppId = self::PROPOSAL_ID)
    {
        return self::API_VERSION.'/proposals/'.$ppId.'/comments';
    }

    public function getEditUrl($ppId = self::PROPOSAL_ID, $commentId)
    {
        return self::API_VERSION.'/proposals/'.$ppId.'/comments/'.$commentId;
    }

    public function getValidParameters()
    {
        return array(
                'title' => 'test',
                'comment' => 'test',
        );
    }

    public function getRequiredParameters()
    {
    }
}
