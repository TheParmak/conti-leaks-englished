#!/bin/bash
FILES='file_path'

for f in $FILES
do
	echo "Processing $f file..."
	# take action on each file. $f store current file name
	sed '/^$/d' "$f" > "$f.2.json"
	cat "$f.2.json" | sed 's/{ "/"/g' > "$f.3.json"
	cat "$f.3.json" | sed 's/\\/{backslash}/g' > "$f.4.json"
	cat "$f.4.json" | sed 's/\"\./"/g' > "$f.5.json"
	cat "$f.5.json" | sed 's/},//g' > "$f"
	rm -rf "$f.2.json" "$f.3.json" "$f.4.json" "$f.5.json"

done

