copied for reference from https://icalendar.org/iCalendar-RFC-5545/3-6-3-journal-component.html this is a formatted version of the relevant part of the [RFC5545](https://www.rfc-editor.org/rfc/rfc5545) spec.

### 3.6.3. Journal Component

#### Component Name

VJOURNAL

#### Purpose

Provide a grouping of component properties that describe a journal entry.

#### Format Definition

A ["VJOURNAL"](https://icalendar.org/iCalendar-RFC-5545/3-6-3-journal-component.html) calendar component is defined by the following notation:



```
 journalc   = "BEGIN" ":" "VJOURNAL" CRLF
              jourprop
              "END" ":" "VJOURNAL" CRLF
```





```
 jourprop   = *(
            ;
            ; The following are REQUIRED,
            ; but MUST NOT occur more than once.
            ;
            dtstamp / uid /
            ;
            ; The following are OPTIONAL,
            ; but MUST NOT occur more than once.
            ;
            class / created / dtstart /
            last-mod / organizer / recurid / seq /
            status / summary / url /
            ;
            ; The following is OPTIONAL,
            ; but SHOULD NOT occur more than once.
            ;
            rrule /
            ;
            ; The following are OPTIONAL,
            ; and MAY occur more than once.
            ;
            attach / attendee / categories / comment /
            contact / description / exdate / related / rdate /
            rstatus / x-prop / iana-prop
            ;
            )
```



#### Description

A ["VJOURNAL"](https://icalendar.org/iCalendar-RFC-5545/3-6-3-journal-component.html) calendar component is a grouping of component properties that represent one or more descriptive text notes associated with a particular calendar date. The ["DTSTART"](https://icalendar.org/iCalendar-RFC-5545/3-8-2-4-date-time-start.html) property is used to specify the calendar date with which the journal entry is associated. Generally, it will have a DATE value data type, but it can also be used to specify a DATE-TIME value data type. Examples of a journal entry include a daily record of a legislative body or a journal entry of individual telephone contacts for the day or an ordered list of accomplishments for the day. The ["VJOURNAL"](https://icalendar.org/iCalendar-RFC-5545/3-6-3-journal-component.html) calendar component can also be used to associate a document with a calendar date.

The ["VJOURNAL"](https://icalendar.org/iCalendar-RFC-5545/3-6-3-journal-component.html) calendar component does not take up time on a calendar. Hence, it does not play a role in free or busy time searches -- it is as though it has a time transparency value of TRANSPARENT. It is transparent to any such searches.

The ["VJOURNAL"](https://icalendar.org/iCalendar-RFC-5545/3-6-3-journal-component.html) calendar component cannot be nested within another calendar component. However, ["VJOURNAL"](https://icalendar.org/iCalendar-RFC-5545/3-6-3-journal-component.html) calendar components can be related to each other or to a ["VEVENT"](https://icalendar.org/iCalendar-RFC-5545/3-6-1-event-component.html) or to a ["VTODO"](https://icalendar.org/iCalendar-RFC-5545/3-6-2-to-do-component.html) calendar component, with the ["RELATED-TO"](https://icalendar.org/iCalendar-RFC-5545/3-8-4-5-related-to.html) property.

#### Example

The following is an example of the ["VJOURNAL"](https://icalendar.org/iCalendar-RFC-5545/3-6-3-journal-component.html) calendar component:



```
 BEGIN:VJOURNAL
 UID:19970901T130000Z-123405@example.com
 DTSTAMP:19970901T130000Z
 DTSTART;VALUE=DATE:19970317
 SUMMARY:Staff meeting minutes
 DESCRIPTION:1. Staff meeting: Participants include Joe\,
   Lisa\, and Bob. Aurora project plans were reviewed.
   There is currently no budget reserves for this project.
   Lisa will escalate to management. Next meeting on Tuesday.\n
  2. Telephone Conference: ABC Corp. sales representative
   called to discuss new printer. Promised to get us a demo by
   Friday.\n3. Henry Miller (Handsoff Insurance): Car was
   totaled by tree. Is looking into a loaner car. 555-2323
   (tel).
 END:VJOURNAL
```



- 
