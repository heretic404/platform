<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Role Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Entity\PermissionRepository;

class Ushahidi_Validator_Role_Update extends Validator
{
	protected $permission_repo;
	protected $feature_enabled;
	protected $default_error_source = 'role';

	public function __construct(PermissionRepository $permission_repo, $feature_enabled)
	{
		$this->permission_repo = $permission_repo;
		$this->feature_enabled = (bool) $feature_enabled;
	}

	protected function getRules()
	{
		return [
			'id' => [
				['numeric'],
			],
			'permissions' => [
				[[$this, 'checkPermissions'], [':validation', ':value']],
				
			],
			'name' => [
				[[$this, 'checkRolesEnabled'], [':validation']],
			],
		];
	}

	public function checkRolesEnabled(Validation $validation, $permissions)
	{
		if (!$this->feature_enabled) {
			$validation->error('name', 'rolesNotEnabled');
			return;
		}
		return;
	}

	public function checkPermissions(Validation $validation, $permissions)
	{
		if (!$permissions) {
			return;
		}

		foreach ($permissions as $permission)
		{
			if (!$this->permission_repo->exists($permission)) {
				$validation->error('permissions', 'permissionDoesNotExist', [$permission]);
				return;
			}
		}
	}
}
