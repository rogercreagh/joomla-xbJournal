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



Calendar object as seen by SimpleCalDAV

```
    [76f5ab1b-e8a8-440b-8086-585b69129582] => CalDAVCalendar Object
        (
            [url:CalDAVCalendar:private] => /remote.php/dav/calendars/roger/76f5ab1b-e8a8-440b-8086-585b69129582/
            [displayname:CalDAVCalendar:private] => Rcloud
            [ctag:CalDAVCalendar:private] => http://sabre.io/ns/sync/405
            [calendar_id:CalDAVCalendar:private] => 76f5ab1b-e8a8-440b-8086-585b69129582
            [rgba_color:CalDAVCalendar:private] => 
            [rbg_color:CalDAVCalendar:private] => #8BC34A
            [order:CalDAVCalendar:private] => 0
        )
```



Journal entry as seen by SimpleCalDav

```
    [2] => CalDAVObject Object
        (
            [href:CalDAVObject:private] => https://cloud.crosborne.uk/remote.php/dav/calendars/roger/76f5ab1b-e8a8-440b-8086-585b69129582/cd268db6-94c8-4bf0-8d6c-6002823a794d.ics
            [data:CalDAVObject:private] => BEGIN:VCALENDAR
VERSION:2.0
PRODID:+//IDN bitfire.at//ical4android
BEGIN:VJOURNAL
DTSTAMP:20221129T073812Z
UID:cd268db6-94c8-4bf0-8d6c-6002823a794d
SEQUENCE:34
CREATED:20221127T081354Z
LAST-MODIFIED:20221129T073806Z
SUMMARY:Apple Fritter Monkey Bread
DESCRIPTION:Made the monkey nuts bread for breakfast. Better than last time
  but still not up to Katy's standard.. Then Anne left to go back to London
 . \n\nGot out to Gwel Dulas after lunch and started making beds in tunel.e
 verything outside very wet\, but clear notcold winter day with spots of .\
 n\nIn evening watched next part of the Kingdom 1 series
STATUS:FINAL
CATEGORIES:GwelDulas,cooking,series
DTSTART;VALUE=DATE:20221127
END:VJOURNAL
END:VCALENDAR

            [etag:CalDAVObject:private] => 354fe31368cc1af767c880b6482317d8
        )
```
