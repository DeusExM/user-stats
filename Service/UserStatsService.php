<?php

namespace FluffyFactory\Bundle\UserStatsBundle\Service;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use FluffyFactory\Bundle\UserStatsBundle\Entity\UserStatsLines;

class UserStatsService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param User $user
     * @param DateTime $begin
     * @param DateTime $end
     * @return UserStatsLines[]
     */
    public function getPageViewsPerPeriod(User $user, DateTime $begin, DateTime $end)
    {
        return $this->em->getRepository(UserStatsLines::class)->findByPeriod($user, $begin, $end);
    }

    /**
     * @param User $user
     * @return array[]
     */
    public function getAvgUtilisation(User $user)
    {
        $allUserStatsLines = $this->em->getRepository(UserStatsLines::class)->findByUser($user);

        $hoursUtilisation = [];
        $dayUtilisation = [];

        /** @var UserStatsLines $allUserStatsLine */
        foreach ($allUserStatsLines as $allUserStatsLine) {
            if (!isset($dayUtilisation[$allUserStatsLine->getCreatedAt()->format('H')])) {
                $dayUtilisation[$allUserStatsLine->getCreatedAt()->format('l')] = 1;
            } else {
                $dayUtilisation[$allUserStatsLine->getCreatedAt()->format('l')]++;
            }

            if (!isset($hoursUtilisation[$allUserStatsLine->getCreatedAt()->format('H')])) {
                $hoursUtilisation[$allUserStatsLine->getCreatedAt()->format('H')] = 1;
            } else {
                $hoursUtilisation[$allUserStatsLine->getCreatedAt()->format('H')]++;
            }
        }

        arsort($hoursUtilisation);
        arsort($dayUtilisation);

        return [
            'hours' => $hoursUtilisation,
            'day' => $dayUtilisation
        ];
    }
}