TechDivision Pnutch - Multithreaded php WebCrawler
===================

# Vorwort

Entstanden ist das Projekt, weil wir für ein Kundenprojekt mehrere Webseiten crawlen müssen und das Ergebnis in
ElasticSearch speichern wollen. Das bekannteste OpenSource Crawler Projekt ist "Apache nutch". Leider ist der Einstieg
in das Projekt äußerst schwer. Es ist in JAVA programmiert und besitzt beinahe keine Dokumentation. Immer wieder gibt es
nicht erklärbare Probleme wie z.B ein stoppen des crawlers. Auch die Plugins erlauben extrem wenig konfiguration bzw.
anpassung. Das PDF Plugin funktioniert mit den Kunden PDFs überhaupt nicht, und es gibt hierzu auch leider keine Alter-
native.

Aus diesem Grund ist "pnutch" entstanden.

# Features

- vollständig in PHP geschrieben
- konfiguration über ini Files
- beliebig erweiterbar
- kann über Browser oder einfach auf der Konsole bedient werden


# Vorraussetzungen

- appserver (min. 0.6.0 beta3)


# Installation

- Projekt klonen
- "ant setup" ausführen
- Gui unter http://127.0.0.1:9080/pnutch/index.do erreichbar


# Konfiguration

unter /opt/appserver/webapps/pnutch/data/configs findet man alle Konfigurationsfiles

## jobs
im jobs Ordner sind die jeweiligen Job konfiguraionen zu finden. Relativ selbsterklärend (exmaple.ini.dist) list im
Ordner. Files die nicht mit .ini enden werden irgnoriert

## PluginMappings
die Auswertung des Contents basiert komplett auf Plugins. Das Mapping, also welcher Content-Type von welchem Plugin
geparst werden soll wird hier definiert. Zusätzlich gibt es Virtuelle Plugins, die sich an ein anderes Plugin hängen
können. So kann zum Beispiel das Language Plugin, welches aus der URL einen LänderCode ausliest einfach an das Content-
Plugin "HtmlParser" gehängt werden.


##ExporterMappings
die Ausgabe ist auch wieder vollkommen auf Plugins gestützt. Das bisher einzige ExporterPlugin exportiert die Daten in
elasticsearch. Die Konfiguration ist so generisch wie möglich gehalten. So gibt es nur den "main" bereich wo "name" (der
angzeigte name) und "plugin" (der PluginName der als identifiert verwendet wird) defniert sein müssen. im Bereich
"configuration" muss je nach PluginAnforderung die Konfiguration angepasst werden. Hier gibt es keinerlei Pflichtfelder

