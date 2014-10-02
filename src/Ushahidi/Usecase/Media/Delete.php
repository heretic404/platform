<?php

/**
 * Ushahidi Platform Admin Media Delete Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Media;

use Ushahidi\Usecase;
use Ushahidi\Data;
use Ushahidi\Entity\Media;
use Ushahidi\Tool\Authorizer;
use Ushahidi\Tool\Validator;
use Ushahidi\Exception\AuthorizerException;
use Ushahidi\Exception\NotFoundException;
use Ushahidi\Exception\ValidatorException;

class Delete implements Usecase
{
	private $repo;
	private $valid;

	public function __construct(DeleteMediaRepository $repo, Validator $valid, Authorizer $auth)
	{
		$this->repo  = $repo;
		$this->valid = $valid;
		$this->auth  = $auth;
	}

	public function interact(Data $input)
	{
		if (!$this->valid->check($input)) {
			throw new ValidatorException('Failed to validate media delete', $this->valid->errors());
		}

		$media = $this->repo->get($input->id);

		if (!$media->id) {
			throw new NotFoundException(sprintf(
				'Media %d does not exist',
				$input->id
			));
		}

		if (!$this->auth->isAllowed($media, 'delete')) {
			throw new AuthorizerException(sprintf(
				'User %s is not allowed to delete media file %s',
				$this->auth->getUserId(),
				$input->id
			));
		}

		$this->repo->deleteMedia($input->id);

		return $media;
	}
}