<?php

namespace Demofmony2\AppBundle\Tests\Manager;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Demofony2\AppBundle\Enum\ProcessParticipationStateEnum;
use Demofony2\AppBundle\Manager\VotePermissionCheckerService;
use Demofony2\AppBundle\Enum\ProposalStateEnum;

class VotePermissionCheckerManagerTest extends WebTestCase
{
    public $vpc;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        /*
         * @var VotePermissionCheckerManager
         */
        $this->vpc = $kernel->getContainer()->get('app.vote_permission_checker');
    }

    /**
     * @expectedException     Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testWhenStateClosedInProcessParticipation()
    {
        $processParticipation =   $this->getMock('\Demofony2\AppBundle\Entity\ProcessParticipation');
        $processParticipation->expects($this->once())->method('getState')->will($this->returnValue(ProcessParticipationStateEnum::CLOSED));
        $this->vpc->checkIfProcessParticipationIsInVotePeriod($processParticipation);
    }

    public function testWhenStateDebateInProcessParticipation()
    {
        $processParticipation =   $this->getMock('\Demofony2\AppBundle\Entity\ProcessParticipation');
        $processParticipation->expects($this->once())->method('getState')->will($this->returnValue(ProcessParticipationStateEnum::DEBATE));
        $result = $this->vpc->checkIfProcessParticipationIsInVotePeriod($processParticipation);
        $this->assertTrue($result);
    }

    /**
     * @expectedException     Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testWhenUserAlreadyVoteInProcessParticipation()
    {
        $processParticipation =   $this->getMock('\Demofony2\AppBundle\Entity\ProcessParticipation');
        $processParticipation->expects($this->once())->method('getId')->will($this->returnValue(1));

        $user =   $this->getMock('\Demofony2\UserBundle\Entity\User');
        $user->expects($this->once())->method('getId')->will($this->returnValue(1));

        // Now, mock the repository so it returns the mock of the employee
        $voteRepository = $this->getMockBuilder('\Demofony2\AppBundle\Repository\VoteRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $voteRepository->expects($this->once())
            ->method('getVoteByUserInProcessParticipation')
            ->will($this->returnValue(1));

        // Last, mock the EntityManager to return the mock of the repository
        $entityManager = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($voteRepository));

        // Last, mock the EntityManager to return the mock of the repository
        $validator = $this->getMock('\Symfony\Component\Validator\Validator\ValidatorInterface');

        $checker = new VotePermissionCheckerService($entityManager, $validator);
        $checker->checkUserHasVoteInProcessParticipation($processParticipation, $user);
    }

    public function testWhenUserNotVoteInProcessParticipation()
    {
        $processParticipation =   $this->getMock('\Demofony2\AppBundle\Entity\ProcessParticipation');
        $processParticipation->expects($this->once())->method('getId')->will($this->returnValue(1));

        $user =   $this->getMock('\Demofony2\UserBundle\Entity\User');
        $user->expects($this->once())->method('getId')->will($this->returnValue(1));

        // Now, mock the repository so it returns the mock of the employee
        $voteRepository = $this->getMockBuilder('\Demofony2\AppBundle\Repository\VoteRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $voteRepository->expects($this->once())
            ->method('getVoteByUserInProcessParticipation')
            ->will($this->returnValue(0));

        // Last, mock the EntityManager to return the mock of the repository
        $entityManager = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($voteRepository));

        // Last, mock the EntityManager to return the mock of the repository
        $validator = $this->getMock('\Symfony\Component\Validator\Validator\ValidatorInterface');

        $checker = new VotePermissionCheckerService($entityManager, $validator);
        $result =  $checker->checkUserHasVoteInProcessParticipation($processParticipation, $user);
        $this->assertTrue($result);
    }

    /**
     * @expectedException     Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testWhenStateClosedInProposal()
    {
        $proposal =   $this->getMock('\Demofony2\AppBundle\Entity\Proposal');
        $proposal->expects($this->once())->method('getState')->will($this->returnValue(ProposalStateEnum::CLOSED));
        $this->vpc->checkIfProposalIsInVotePeriod($proposal);
    }

    /**
     * @expectedException     Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testWhenUserAlreadyVoteInProposal()
    {
        $proposal =   $this->getMock('\Demofony2\AppBundle\Entity\Proposal');
        $proposal->expects($this->once())->method('getId')->will($this->returnValue(1));

        $user =   $this->getMock('\Demofony2\UserBundle\Entity\User');
        $user->expects($this->once())->method('getId')->will($this->returnValue(1));

        // Now, mock the repository so it returns the mock of the employee
        $voteRepository = $this->getMockBuilder('\Demofony2\AppBundle\Repository\VoteRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $voteRepository->expects($this->once())
            ->method('getVoteByUserInProposal')
            ->will($this->returnValue(1));

        // Last, mock the EntityManager to return the mock of the repository
        $entityManager = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($voteRepository));

        // Last, mock the EntityManager to return the mock of the repository
        $validator = $this->getMock('\Symfony\Component\Validator\Validator\ValidatorInterface');

        $checker = new VotePermissionCheckerService($entityManager, $validator);
        $checker->checkUserHasVoteInProposal($proposal, $user);
    }

    public function testWhenUserNotVoteInProposal()
    {
        $proposal =   $this->getMock('\Demofony2\AppBundle\Entity\Proposal');
        $proposal->expects($this->once())->method('getId')->will($this->returnValue(1));

        $user =   $this->getMock('\Demofony2\UserBundle\Entity\User');
        $user->expects($this->once())->method('getId')->will($this->returnValue(1));

        // Now, mock the repository so it returns the mock of the employee
        $voteRepository = $this->getMockBuilder('\Demofony2\AppBundle\Repository\VoteRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $voteRepository->expects($this->once())
            ->method('getVoteByUserInProposal')
            ->will($this->returnValue(0));

        // Last, mock the EntityManager to return the mock of the repository
        $entityManager = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($voteRepository));

        // Last, mock the EntityManager to return the mock of the repository
        $validator = $this->getMock('\Symfony\Component\Validator\Validator\ValidatorInterface');

        $checker = new VotePermissionCheckerService($entityManager, $validator);
        $result =  $checker->checkUserHasVoteInProposal($proposal, $user);
        $this->assertTrue($result);
    }
}
