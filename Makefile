.PHONY: dist help send permissions
VERSION=$(shell cat VERSION)
PACKAGE=gcourrier-$(VERSION)
HTTPD_USER=www-data

# Default target
help:        # Available targets
	@echo "- Targets -"
	@-grep ^[a-z]\\+: Makefile

dist:        # Tarball
	mkdir $(PACKAGE)/
	git archive master | tar -x -C $(PACKAGE)/
	make -C $(PACKAGE)/doc
	cp $(PACKAGE)/config.php.dist $(PACKAGE)/config.php
	tar czf $(PACKAGE).tar.gz $(PACKAGE)
	rm -rf $(PACKAGE)

send:        # Publish to gcourrier.cliss21.com
	scp $(PACKAGE).tar.gz gcourrier@gcourrier.cliss21.com:www/fichier

permissions: # Set file permissions (chmod)
	chown root:root -R .
	find -type d -print0 | xargs -r0 chmod 755
	find -type f -print0 | xargs -r0  chmod 644
	chgrp $(HTTPD_USER) accuse/ upload/ config.php
	chmod 775 accuse/ upload/
	-chmod 640 config.php
	chmod 700 upgrades
