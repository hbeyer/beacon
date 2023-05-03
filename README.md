# Beacon: Service zum Anzeigen von Personeninformationen anhand der GND-Nummer

Ein Webservice zum Generieren von biographischen Links aus einem Set von Beacon-Dateien, der zugleich die zugehörigen Personendaten aus der GND anzeigt. Die Beacon-Dateien werden von der Anwendung gecached und können per Skript aktualisiert werden. Die Liste der Datenquellen kann händisch bearbeitet werden. In der vorliegenden Version ist die Anwendung für die HAB Wolfenbüttel programmiert. Ressourcen der HAB werden gesondert hervorgehoben.

##  Technisches
Die Anwendung ist in PHP geschrieben (Version 5.6 oder höher). Sie verwendet das Templatesystem Twig (Version 1.42) und die Symfony-Komponente Yaml (Version 2.2).

### Klassen
Der eigene Quellcode liegt unter [src/HAB](src/HAB). Folgende Klassen werden bereitgestellt:

#### \HAB\GND
Klasse zur Validierung einer GND-Nummer. Methoden:
 - __construct(\$string): Validiert die als `$string` übergebene GND-Nummer, das Ergebnis steht in der Eigenschaft `valid`.
- __toString(): Gibt die GND-Nummer aus.

#### \HAB\RequestGND
Die Klasse automatisiert eine Anfrage an die Metadatenschnittstelle http://hub.culturegraph.org/entityfacts/{id}. Methoden:
- __construct(GND \$gnd): Lädt die Daten von der Schnittstelle und legt sie in den Properties des Objekts ab, dies sind: `preferredName`, `type`, `variantNames` (Array), `info`, `dateBirth`, `placeBirth`, `dateDeath`, `placeDeath`, `placesActivity` (Array), `academicDegree`, `relations` (Array mit mehreren assoziativen Arrays), `familiarRelarions` (Array mit mehreren assoziativen Arrays).

#### \HAB\BeaconRepository
Verwaltung einer Sammlung von Beacon-Dateien, die lokal gecached werden, Durchsuchen der Dateien und Erzeugung von Links auf Nachweissysteme für Personen. Methoden:
- __construct(\$update = true): Einlesen der Datenquellen, Validieren der Dateien und Ordner, Aktualisieren der Beacon-Dateien, sofern welche fehlen oder das letzte Update zu lange her ist (s. Eigenschaft update_int). Über den Parameter \$update kann das Update unterdrückt werden.
- update(): Update der gecacheten Beacon-Dateien, Abspeichern eines Unix-Timestamp unter changeDate im selben Ordner.
- getLinks(GND \$gnd, \$target = false): Durchsuchen der Beacon-Dateien auf Treffer für eine GND, Ausgabe von Links im HTML-Format.
- getSelectedLinks(gnd \$gnd, \$hab = true, \$target = ''): Dasselbe wie getLinks(), nur dass wenn der Parameter \$hab auf true gesetzt ist, nur die Quellen ausgewertet werden, die in der Eigenschaft `sourcesHAB`angegeben sind. Im anderen Fall werden nur alle anderen Datenquellen berücksichtigt. Über den Parameter \$target kann beeinflusst werden, ob die Links in einem neuen Tab geöffnet werden. 
- getLinksMulti(\$gndArray, $target = ''): Überprüfen von mehreren GND-Nummern, die als Array übergeben werden. Rückgabewert ist ein Array von Links im HTML-Format. Über den Parameter \$target kann beeinflusst werden, ob die Links in einem neuen Tab geöffnet werden.
- getTypeArray(): Ausgabe der Datenquellen, gruppiert nach der in [sources.yml](data/sources.yml) hinterlegten Eigenschaft `dbtype`.
- validate(): Validieren des Objekts auf Integrität, d. h. Vorhandensein aller Ordner und Dateien.

### Codebeispiel
```php
$repository = new \HAB\BeaconRepository();
$gnd = new \HAB\GND('136201733');
$request = new \HAB\RequestGND($gnd);
$links = $repository->getLinks($gnd, '_blank');
echo $request->preferredName.':<br>';
echo implode('<br>', $links);
```
Dies erzeugt die Ausgabe:
```html
Melusine von der Schulenburg:<br>
<a href="http://tools.wmflabs.org/persondata/redirect/gnd/de/136201733" target="_blank">Wikipedia</a><br>
<a href="http://www.deutsche-biographie.de/pnd136201733.html" target="_blank">Deutsche Biographie</a><br>
<a href="http://jdgdb.bbaw.de/cgi-bin/jdg?t_idn_erg=x&idn=GND:136201733" target="_blank">Jahresberichte für deutsche Geschichte</a>
```

### Installation
Die Anwendung ist auf einem Server mit PHP 5.6 oder höher lauffähig. Für eine handliche URL muss der ggf. einzurichtende virtuelle Host auf den Ordner [public](public) zeigen, wo das Skript [index.php](public/index.php) ausgeführt wird.

Sofern Docker installiert ist, kann die Anwendung mit den in der Datei [docker-compose.yml](docker-compose.yml) definierten Einstellungen zum Laufen gebracht werden. Dafür muss im selben Verzeichnis folgender Befehl ausgeführt werden: 
```bash
docker-compose up -d
```
Sie läuft dann unter der Adresse http://localhost:82/public/. 

## Administration

### Datenquellen
Die Datenquellen sind in der Datei [data/sources.yml](data/sources.yml) festgelegt. Die Auswahl beruht im Wesentlichen auf der Seite https://de.wikipedia.org/wiki/Wikipedia:BEACON. Hinzu kommen lokale Quellen zu Beständen der HAB. Damit Änderungen wirksam werden, muss erst ein Update durchgeführt werden. Für jede Datenquelle muss eine eindeutige ID vergeben werden, dazu kommen folgende Felder:
- label: Die Benennung des Nachweissystems
- location: Die Adresse, von der die Beacon-Datei heruntergeladen werden kann.
- target: Das Muster, mit dem für einzelne GND-Nummern URLs gebildet werden können. In der Regel steht dies in der Beacon-Datei unter #target.
- type: "default", sofern eine URL mit GND-Nummer und target gebildet word, "specified", sofern in der Beacon-Datei hinter der GND-Nummer interne IDs bzw. eigene URLs angegeben sind.
- dbtype: Eine frei zu vergebende Angabe des Datenbanktyps wie z. B. "Regionalbiographie". Diese steuern die Anzeige der Datenquellen auf der Website.

### Update
Das Update wird entweder beim Instanziieren der Klasse `BeaconRepository` durchgeführt, wenn mehr Sekunden als in der Eigenschaft `BeaconRepository::update_int` angegeben vergangen sind, oder es kann mit dem Skript [update.php](public/update.php) manuell angestoßen werden. Hierbei werden alle Beacon-Dateien unterschiedslos im Ordner [data/beaconFiles](data/beaconFiles) neu geladen, obsolete werden aber nicht gelöscht.

Fehlgeschlagene Downloadversuche werden in der Ausgabe angezeigt. Sofern der Download an einem unbekannten Zertifikat scheitert, kann man das Zertifikat manuell herunterladen und an die Datei [certs/collection.pem](certs/collection.pem) anhängen.