<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The mod_forum userreport viewed event.
 *
 * @package    mod_forum
 * @copyright  2014 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_forum userreport viewed event.
 *
 * @property-read array $other Extra information about the event.
 *     - string reportmode: The mode the report has been viewed in (posts or discussions).
 *
 * @package    mod_forum
 * @copyright  2014 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class userreport_viewed extends \core\event\base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user {$this->userid} has viewed the userreport for user {$this->relateduserid} in ".
            "course {$this->courseid} with viewing mode {$this->other['reportmode']}";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventuserreportviewed', 'mod_forum');
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {

        $url = new \moodle_url('/mod/forum/user.php', array('id' => $this->relateduserid,
            'mode' => $this->other['reportmode']));

        if ($this->courseid != SITEID) {
            $url->param('course', $this->courseid);
        }

        return $url;
    }

    /**
     * Return the legacy event log data.
     *
     * @return array|null
     */
    protected function get_legacy_logdata() {
        // The legacy log table expects a relative path to /mod/forum/.
        $logurl = substr($this->get_url()->out_as_local_url(), strlen('/mod/forum/'));

        return array($this->courseid, 'forum', 'user report', $logurl, $this->relateduserid);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();
        if (!isset($this->relateduserid)) {
            throw new \coding_exception('relateduserid must be set.');
        }
        if (!isset($this->other['reportmode'])) {
            throw new \coding_exception('reportmode must be set in other.');
        }

        switch ($this->contextlevel)
        {
            case CONTEXT_COURSE:
            case CONTEXT_SYSTEM:
                // OK, expected context level.
                break;
            default:
                // Unexpected contextlevel.
                throw new \coding_exception('Context passed must be system or course.');
        }
    }

}

