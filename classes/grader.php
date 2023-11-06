<?php
// This file is part of CodeRunner - http://coderunner.org.nz/
//
// CodeRunner is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// CodeRunner is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with CodeRunner.  If not, see <http://www.gnu.org/licenses/>.
/** The base class for the coderunner Grader classes.
 *  A Grader is called after running all testcases in a sandbox.
 *  Graders have an external name, which appears in the exported .xml question
 *  files for example, and a classname and a filename in which the class is
 *  defined.
 *  to confirm the correctness of the results.
 *  In the simplest subclass, qtype_coderunner_equality_grader, a test result is correct if
 *  the actual and expected outputs are identical after trailing white space
 *  has been removed. More complicated subclasses can, for example, do
 *  things like regular expression testing.
 */

/**
 * @package    qtype_coderunner
 * @copyright  Richard Lobb, 2012, The University of Canterbury
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

abstract class qtype_coderunner_grader {
    /** Check all outputs, returning an array of TestResult objects.
     * A TestResult is an object with expected, got, isCorrect and grade fields.
     * 'got' and 'expected' fields are sanitised by replacing embedded
     * control characters with hex equivalents and by limiting their
     * lengths to MAX_STRING_LENGTH.
     */

    // Return the name of this grader - one of available_graders() below.
    abstract public function name();

    /**
     * A list of available graders. Keys are the externally known grader names
     * as they appear in the exported questions, values are the associated
     * class names. File names are the same as the class names with the
     * leading qtype_coderunner and all underscores removed.
     * @return array
     */
    public static function available_graders() {
        return array('EqualityGrader'       => 'qtype_coderunner_equality_grader',
                     'NearEqualityGrader'   => 'qtype_coderunner_near_equality_grader',
                     'RegexGrader'          => 'qtype_coderunner_regex_grader',
                     'TemplateGrader'       => 'qtype_coderunner_template_grader'
                );
    }

    /** Called to grade the output generated by a student's code for
     *  a given testcase. Returns a single TestResult object, which
     *  must have called tidy on the expected, output and stdin fields
     *  to limit their lengths.
     */
    public function grade(&$output, &$testcase, $isbad = false) {
        if ($isbad) {
            $outcome = new qtype_coderunner_test_result($testcase, false, 0.0, $output);
        } else {
            $outcome = $this->grade_known_good($output, $testcase);
        }
        return $outcome;
    }


    abstract protected function grade_known_good(&$output, &$testcase);

}
