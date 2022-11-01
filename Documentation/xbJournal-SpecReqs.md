The basic structure of the xbJournal data will follow the RFC5545 VJOURNAL spec.

The spec defines the following properties for the VJOURNAL component with possible mapping to Joomla component elements:

Required once only

- DTSTAMP               <=>             entrydate
- UID 	                     <=>             uid (created from item id )

Optional once only

- CLASS
- CREATED
- DTSTART
- LAST-MOD
- ORGANIZER
- RECURID
- SEQ
- STATUS
- SUMMARY
- URL
- RRULE

Optional may occur more than once

- ATTACH
- ATTENDEE
- CATEGORIES
- COMMENT
- CONTACT
- DESCRIPTION
- EXDATE
- RELATED
- RDATE
- RSTATUS
- X-PROP
- IANA-PROP

