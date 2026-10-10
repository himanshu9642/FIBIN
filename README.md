# FIBIN 4.2 — Food Donation & Community Platform

PHP + MySQL + HTML + CSS + JavaScript project for XAMPP.

## Fixed in 4.2
- Admin login now redirects directly to the Admin Command Center.
- Admin access refreshes the user's role from MySQL, so an old session cannot block a real admin.
- Demo `admin@fibin.org` is automatically repaired to the `admin` role.
- Admin can upload food with a real JPG/PNG/WebP image.
- Admin can moderate food, requests, users and community posts.

## Community Hub
- Beautiful purple/white activity feed.
- Users and admins can publish activity posts.
- Optional activity photo upload.
- Likes, comments and @mentions.
- Private messaging.
- People directory with profile, Chat and Meet actions.
- Users and admins can create future community meetings.
- Every meeting receives a unique Jitsi Meet room.
- Join/copy meeting links directly from Community.
- Admins can delete any community activity; users can delete their own.

## XAMPP
1. Extract the project into `C:\xampp\htdocs\FIBIN_Food_Donation`.
2. Start Apache and MySQL.
3. Import `database.sql` into phpMyAdmin for a fresh install.
4. If using the older FIBIN database, import `database_upgrade.sql`.
5. Open `http://localhost/FIBIN_Food_Donation/`.

## Demo admin
Email: `admin@fibin.org`
Password: `admin123`

## Upload folders
- `uploads/foods/`
- `uploads/avatars/`
- `uploads/community/` (created automatically on first activity-photo upload)

## Video meetings
FIBIN uses Jitsi Meet links for browser-based video meetings. A meeting is created only when a logged-in user/admin submits the meeting form.

## FIBIN 4.6 Admin + User Navigation Upgrade

### Admin Control Center
The Admin Panel now has dedicated working modules for:
- Dashboard
- Users
- Food Listings
- NGOs
- Volunteers
- Donations
- Deliveries
- Campaigns
- Community
- Reports
- Analytics

### User Navigation
The main navigation now includes:
- Home
- About
- Browse Food
- Donate Food
- NGOs
- Volunteers
- Campaigns
- Community
- Nearby
- Dashboard
- Messages
- Profile
- Notifications

Community also exposes Feed, Messages, Groups, Events and Meetings.

### Database upgrade
After importing `database.sql`, import `database_upgrade.sql` and then `database_upgrade_v2.sql` in phpMyAdmin. The v2 upgrade adds NGO, volunteer, campaign, delivery, report, group and event tables.
