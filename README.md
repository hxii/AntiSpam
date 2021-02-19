# Simple Anti-Spam by hxii

## Preface
On my [website](https://0xff.nu) I use [FormSubmit.io](https://formsubmit.io/) in order to allow readers to get in touch with me without disclosing my email address.
Lately I've noticed that despite FormSubmit having some sort of anti-spam measures, I am still getting [trash](https://0xff.nu/i/Alert-a7567487ba60f193.jpg) to my email inbox. I figured these must be bots, so I decided to make a somewhat simple honeypot solution to try and address this issue.

**Note:** Use this code at your own risk. I am currently still testing whether this actually works (it did work in testing) and whether it's effective. If you've got any ideas how it could be improved, let me know!

## Operation
**`form.html`** is a fairly simple contact form that uses `redirector.php` as the action: `<form id="contactform" action="/redirector.php" method="POST">`
It contains two honeypot fields (in my use-case) of two different types:
- A `type=hidden` field: `<input name="email" id="email" type="hidden" value="">`
- A regular `type=text` field that is hidden with CSS: `<input name="name" id="name" type="text" autocomplete="off" style="opacity:0;position:absolute;z-index:-1;top:0;left:0;height:0;width:0" tabindex="-1">`

**`redirector.php`** checks if the current visitor is already banned from sending forms, bans them if honeypot fields are triggerred, forwards the request to FormSubmit and shows an error message.

**`formspam.php`** contains all the logic.

## Notes
- You will probably want to restrict access to both `log.txt` and `rules.txt` any way you choose, e.g. placing the files outside of `htdocs`.
- Each infraction is logged, and returns the visitor a reference ID which you can find in the log along with the visitor details and the rule that lead to the ban.
- This code is most likely incomplete, and can be improved in many ways. If you think of something - let me know!