<?php
namespace Phpeople\Git;

class GitLogParser {

    /**
     * %H   commit hash
     * %aN  author name
     * %aE  author email
     * %ai  author date, ISO 8601 format
     * %cN  committer name
     * %cE  committer email
     * %ci  committer date, ISO 8601 format
     * %s   subject
     * %n   <newline>
     */
    const PRETTY_FORMAT_STRING = 'H:%H%naN:%aN%naE:%aE%nai:%ai%ncN:%cN%ncE:%cE%nci:%ci%ns:%s%nb:%b%n%n';

    /**
     * @return Commit[]
     */
    public function getCommits() {
        exec(sprintf('git log --pretty=format:"%s"', self::PRETTY_FORMAT_STRING), $output);
        $commits = [];
        $commitData = [];
        foreach ($output as $line) {
            if (empty($line)) {
                $commits[$commitData['H']] = new Commit(
                    new Hash($commitData['H']),
                    new User($commitData['aN'], $commitData['aE']),
                    new \DateTime($commitData['ai']),
                    new User($commitData['cN'], $commitData['cE']),
                    new \DateTime($commitData['ci']),
                    $commitData['s']
                );
                continue;
            }
            if (false === strpos($line, ':')) {
                continue;
            }
            list($key, $value) = explode(':', $line, 2);
            $commitData[$key] = $value;
        }
        return $commits;
    }

}