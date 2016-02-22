<?php

namespace TYPO3\T3extblog\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Felix Nagel <info@felixnagel.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * BackendDashboardController
 */
class BackendDashboardController extends BackendBaseController {

	/**
	 * Blog dashboard
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->view->assignMultiple(array(
			'postDrafts' => $this->postRepository->findDrafts($this->pageId),
			'comments' => $this->commentRepository->findByPage($this->pageId),
			'pendingComments' => $this->commentRepository->findPendingByPage($this->pageId),
			'postSubscribers' => $this->postSubscriberRepository->findByPage($this->pageId, FALSE),
			'blogSubscribers' => $this->blogSubscriberRepository->findByPage($this->pageId, FALSE),
			// For statistic
			'postCount' => $this->postRepository->findByPage($this->pageId)->count(),
			'validCommentsCount' => $this->commentRepository->findValid($this->pageId)->count(),
			'validPostSubscribersCount' => $this->postSubscriberRepository->findByPage($this->pageId)->count(),
			'validBlogSubscribersCount' => $this->blogSubscriberRepository->findByPage($this->pageId)->count()
		));
	}

}
