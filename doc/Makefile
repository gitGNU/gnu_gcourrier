.PHONY: clean all
LYX=lyx
# or: LYX=lyx-qt
# dynamic override: make sthing.pdf LYX=lyx-qt

all: doc_utilisateur.ps doc_utilisateur.pdf

%.pdf: %.lyx
	$(LYX) $< -e pdf

%.ps: %.lyx
	$(LYX) $< -e ps

clean:
	rm *.ps *.pdf
