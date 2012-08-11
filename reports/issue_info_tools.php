<?php
/*
   This file is part of CoDev-Timetracking.

   CoDev-Timetracking is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   CoDev-Timetracking is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with CoDev-Timetracking.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once('tools.php');

class IssueInfoTools {

   /**
    * Initialize complex static variables
    * @static
    */
   public static function staticInit() {
      // Nothing special
   }

   /**
    * Get general info of an issue
    * @param Issue $issue The issue
    * @param bool $isManager if true: show MgrEffortEstim column
    * @param bool $displaySupport If true, display support
    * @return mixed[]
    */
   public static function getIssueGeneralInfo(Issue $issue, $isManager=false, $displaySupport=false) {
      $withSupport = true;  // include support in elapsed & Drift

      $drift = $issue->getDrift($withSupport);
      $issueGeneralInfo = array(
         "issueId" => $issue->bugId,
         "issueSummary" => htmlspecialchars($issue->summary),
         "issueExtRef" => $issue->tcId,
         'mantisURL'=> Tools::mantisIssueURL($issue->bugId, NULL, true),
         'issueURL' => Tools::mantisIssueURL($issue->bugId),
         'statusName'=> $issue->getCurrentStatusName(),
         'handlerName'=> UserCache::getInstance()->getUser($issue->handlerId)->getName(),

         "issueEffortTitle" => $issue->effortEstim.' + '.$issue->effortAdd,
         "issueEffort" => $issue->effortEstim + $issue->effortAdd,
         "issueReestimated" => $issue->getReestimated(),
         "issueBacklog" => $issue->backlog,
         "issueDriftColor" => $issue->getDriftColor($drift),
         "issueDrift" => round($drift, 2),
         "progress" => round(100 * $issue->getProgress())
      );
      if($isManager) {
         $issueGeneralInfo['issueMgrEffortEstim'] = $issue->mgrEffortEstim;
         $issueGeneralInfo['issueReestimatedMgr'] = $issue->getReestimatedMgr();
         $driftMgr = $issue->getDriftMgr($withSupport);
         $issueGeneralInfo['issueDriftMgrColor'] = $issue->getDriftColor($driftMgr);
         $issueGeneralInfo['issueDriftMgr'] = round($driftMgr, 2);
      }
      if ($withSupport) {
         $issueGeneralInfo['issueElapsed'] = $issue->getElapsed();
      } else {
         $job_support = Config::getInstance()->getValue(Config::id_jobSupport);
         $issueGeneralInfo['issueElapsed'] = $issue->getElapsed() - $issue->getElapsed($job_support);
      }
      if ($displaySupport) {
         if ($isManager) {
            $driftMgr = $issue->getDriftMgr(!$withSupport);
            $issueGeneralInfo['issueDriftMgrSupportColor'] = $issue->getDriftColor($driftMgr);
            $issueGeneralInfo['issueDriftMgrSupport'] = round($driftMgr, 2);
         }
         $drift = $issue->getDrift(!$withSupport);
         $issueGeneralInfo['issueDriftSupportColor'] = $issue->getDriftColor($drift);
         $issueGeneralInfo['issueDriftSupport'] = round($drift, 2);
      }

      return $issueGeneralInfo;
   }

}

// Initialize complex static variables
IssueInfoTools::staticInit();

?>
