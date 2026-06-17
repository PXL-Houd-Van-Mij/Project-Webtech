Readme · MDCopyReceptify 🍳

Een receptenwebsite gebouwd voor het vak Webtechnologie aan PXL. Gebruikers kunnen recepten ontdekken, zelf recepten uploaden, favorieten bewaren, recepten liken en op tags/specialiteiten filteren. Daarnaast is er een admin-paneel voor beheer en een dagelijks wisselend "Recept van de dag".

⚙️ Technologieën


PHP – server-side logica en databasecommunicatie
MySQL (via MariaDB) – opslag van gebruikers, recepten, favorieten en tags
HTML & CSS – opmaak en styling
JavaScript – likes, tabs en de countdown van het recept van de dag
XAMPP (Apache + MySQL) – lokale ontwikkelomgeving
TheMealDB API – externe recepten voor het recept van de dag


🚀 Lokaal opstarten


Installeer XAMPP.
Plaats de projectmap in C:\xampp\htdocs\ (bijvoorbeeld C:\xampp\htdocs\Project-Webtech\).
Start in de XAMPP Control Panel zowel Apache als MySQL.
Open phpMyAdmin, maak een database aan (bijv. receptify) en importeer db.sql.
Pas in db.php de databasenaam aan zodat die overeenkomt met de aangemaakte database.
Open de site via http://localhost/Project-Webtech/index.php.



Let op: de map waarin het project staat bepaalt de URL. Staat het in htdocs\Project-Webtech, dan is de URL localhost/Project-Webtech/.... Werk bij voorkeur rechtstreeks in de htdocs-map zodat wijzigingen meteen zichtbaar zijn.



🗄️ Database

De standaard XAMPP-instellingen in db.php:


host: localhost
gebruiker: root
wachtwoord: (leeg)
database: de naam die je in phpMyAdmin aanmaakte


Importeer db.sql om alle tabellen (gebruikers, recepten, favorieten, tags, enz.) aan te maken.

📁 Bestandsoverzicht

Algemeen


index.php – homepage met aanbevolen recepten
navbar.php – navigatiebalk (op elke pagina ingevoegd)
footer.php – voettekst
style.css – algemene styling
script.js – algemene JavaScript (o.a. likes)
db.php – databaseverbinding
db.sql – databasestructuur


Gebruikers & authenticatie


register.php – account registreren
login.php – inloggen
logout.php – uitloggen
profiel.php – persoonlijk profiel met eigen recepten en favorieten


Recepten


recept.php – detailpagina van een recept
upload.php – nieuw recept uploaden
edit_upload.php / edit_recept.php – recept bewerken
delete_recept.php – recept verwijderen
favorieten.php – opgeslagen favorieten
toggle_like.php – like toevoegen/verwijderen
recept_van_de_dag.php – dagelijks wisselend recept via TheMealDB
recept_dag_cache.json – cache zodat het recept een dag stabiel blijft


Tags & specialiteiten


tag.php – recepten per tag bekijken
specialiteiten.php – recepten gemarkeerd als specialiteit


Extra


videos.php – pagina met video's
helpdesk.php – helpdesk-/contactpagina
report_recept.php – een recept rapporteren


Admin


admin_login.php / admin_logout.php – admin-authenticatie
admin_panel.php – beheerdashboard
admin_tags.php / admin_subtags.php – tags en subtags beheren
admin_specialiteiten.php – specialiteiten beheren
delete_tag.php / delete_subtag.php / delete_specialiteit.php – beheeritems verwijderen
delete_recept_admin.php – recept verwijderen als admin
remove_report.php – rapportage afhandelen


✨ Functionaliteiten


Registratie, login en sessiebeheer
Recepten uploaden met afbeelding, ingrediënten en bereiding
Recepten liken en als favoriet opslaan
Filteren op tags, subtags en specialiteiten
Dagelijks wisselend "Recept van de dag" met countdown
Recepten rapporteren
Admin-paneel voor het beheren van recepten, tags en rapportages


👥 Gemaakt door

Tom, Luuk en Stef — PXL, Bachelor Elektronica-ICT.

📄 Licentie

Dit project valt onder de MIT-licentie.
