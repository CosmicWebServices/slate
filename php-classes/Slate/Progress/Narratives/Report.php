<?php

namespace Slate\Progress\Narratives;

use Emergence\People\Relationship;

class Report extends \VersionedRecord
{
    // ActiveRecord configuration
    public static $tableName = 'narrative_reports';
    public static $singularNoun = 'narrative report';
    public static $pluralNoun = 'narrative reports';
    public static $collectionRoute = '/progress/narratives/reports';
    public static $updateOnDuplicateKey = true;
    public static $trackModified = true;

    // required for shared-table subclassing support
    public static $defaultClass = __CLASS__;
    public static $subClasses = [__CLASS__];

    public static $printTemplate = 'print';

    public static $fields = [
        'TermID' => [
            'type' => 'integer',
            'unsigned' => true
        ],
        'CourseSectionID' => [
            'type' => 'integer',
            'unsigned' => true
        ],
        'StudentID' => [
            'type' => 'integer',
            'unsigned' => true
        ],
        'Status' => [
            'type' => 'enum',
            'values' => ['draft', 'published'],
            'default' => 'draft'
        ],
        'Notes' => [
            'type' => 'clob',
            'notnull' => false
        ]
    ];

    public static $indexes = [
        'NarrativeReport' => [
            'fields' => ['TermID', 'CourseSectionID', 'StudentID'],
            'unique' => true
        ]
    ];

    public static $relationships = [
        'Section' => [
            'type' => 'one-one',
            'class' => \Slate\Courses\Section::class,
            'local' => 'CourseSectionID'
        ],
        'Student' => [
            'type' => 'one-one',
            'class' => \Slate\People\Student::class
        ],
        'Term' => [
            'type' => 'one-one',
            'class' => \Slate\Term::class
        ],
        'SectionNotes' => [
            'type' => 'one-one',
            'class' => SectionNotes::class,
            'link' => ['TermID', 'CourseSectionID']
        ]
    ];

    public static $searchConditions = [
        'narrativeID' => [
            'qualifiers' => ['narrativeid'],
            'points' => 2,
            'sql' => 'ID=%u'
        ],
        'termID' => [
            'qualifiers' => ['termid'],
            'points' => 2,
            'sql' => 'TermID=%u'
        ],
        'studentID' => [
            'qualifiers' => ['studentid'],
            'points' => 2,
            'sql' => 'StudentID=%u'
        ],
        'authorID' => [
            'qualifiers' => ['authorid'],
            'points' => 2,
            'sql' => 'CreatorID=%u'
        ],
        'advisorID' => [
            'qualifiers' => ['advisorid'],
            'points' => 2,
            'sql' => 'StudentID in (Select ID from people where AdvisorID=%u)'
        ]
    ];

    public static $dynamicFields = [
        'Student',
        'SelfRecipients' => [
            'method' => 'getSelfRecipients'
        ],
        'AdvisorRecipients' => [
            'method' => 'getAdvisorRecipients'
        ],
        'GuardianRecipients' => [
            'method' => 'getGuardianRecipients'
        ],
    ];

    public function getSelfRecipients() {
        $recipients = [];
        $student = $this->Student;

        if ($student->PrimaryEmailID && \Validators::email($student->Email)) {
            $recipients[] = [
                'emailName' => $student->FullName,
                'emailAddress' => $student->Email,
                'emailRecipient' => $student->EmailRecipient
            ];
        }

        return $recipients;

    }

    public function getAdvisorRecipients() {
        $recipients = [];
        $student = $this->Student;
        $advisor = $student->Advisor;

        if ($advisor && $advisor->PrimaryEmailID && \Validators::email($advisor->Email)) {
            $recipients[] = [
                'emailName' => $advisor->FullName,
                'emailAddress' => $advisor->Email,
                'emailRecipient' => $advisor->EmailRecipient
            ];
        }

        return $recipients;
    }

    public function getGuardianRecipients() {
        $recipients = [];
        $student = $this->Student;

        $guardianRelationships = Relationship::getAllByWhere([
            'PersonID' => $student->ID,
            'Class' => \Emergence\People\GuardianRelationship::class
        ]);

        foreach($guardianRelationships as $guardianRelationship) {
            $relatedPerson = $guardianRelationship->RelatedPerson;
            if ($relatedPerson->PrimaryEmailID && \Validators::email($relatedPerson->Email)) {
                $recipients[] = [
                    'emailName' => $relatedPerson->FullName,
                    'emailAddress' => $relatedPerson->Email,
                    'emailRecipient' => $relatedPerson->EmailRecipient
                ];
            }
        }

        return $recipients;
    }
}
