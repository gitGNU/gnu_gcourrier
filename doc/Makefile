.PHONY: clean all

all: doc_utilisateur.ps doc_utilisateur.pdf

%.pdf: %.lyx
	lyx $< -e pdf

%.ps: %.lyx
	lyx $< -e ps

clean:
	rm *.ps *.pdf
