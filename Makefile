.PHONY: dist
VERSION=$(shell cat VERSION)
PACKAGE=gcourrier-$(VERSION)

# Default target
all:
	@echo "To create a tarball, type 'make dist'."

# Tarball
dist:
	tla export $(PACKAGE)
	make -C $(PACKAGE)/doc
	tar czf $(PACKAGE).tar.gz $(PACKAGE)
	rm -rf $(PACKAGE)

send:
	scp $(PACKAGE).tar.gz gcourrier@gcourrier.cliss21.com:www/fichier
