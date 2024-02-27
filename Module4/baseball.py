import os
import sys
import re

if len(sys.argv) < 2:
    sys.exit(f"Usage: {sys.argv[0]} filename")

filename = sys.argv[1]
# print(filename)

if not os.path.exists(filename):
    sys.exit(f"Error: File '{sys.argv[1]}' not found")

def getinfo(line, players):
    regex = re.compile(r"\b(\w+ \w+) batted (\d+) times with (\d+) hits and (\d+) runs\b")
    matches = re.match(regex, line)
    if matches is not None:
        # print(matches)
        name = matches.group(1)
        times = float(matches.group(2))
        hits = float(matches.group(3))
        if name not in players.keys():
            players.update({name: [times, hits]})
        else:
            players[name][0] += times
            players[name][1] += hits
    return players

players = {}
averages = []
with open(filename) as f:
    for line in f:
        player = getinfo(line, players)

for player in players:
    averages.append((player, (players[player][1] / players[player][0])))
# print(averages)

averages.sort(key = lambda x: x[1], reverse=True)

for player, average in averages:
    print(f"{player}: {average:.3f}")


# python3 baseball.py cardinals-1940.txt
# Pepper Martin: 0.316
# Walker Cooper: 0.316
# Johnny Mize: 0.314
# Ernie Koy: 0.310
# Enos Slaughter: 0.306
# Joe Medwick: 0.304
# Terry Moore: 0.304
# Joe Orengo: 0.286
# Jimmy Brown: 0.280
# Marty Marion: 0.279
# Don Gutteridge: 0.276
# Johnny Hopp: 0.275
# Creepy Crespi: 0.273
# Mickey Owen: 0.265
# Bill DeLancey: 0.250
# Don Padgett: 0.242
# Stu Martin: 0.238
# Eddie Lake: 0.222
# Hal Epps: 0.214
# Lon Warneke: 0.209
# Harry Walker: 0.185
# Max Lanier: 0.179
# Bill McGee: 0.178
# Carl Doyle: 0.174
# Mort Cooper: 0.157
# Clyde Shoun: 0.145
# Carden Gillenwater: 0.130
# Bob Bowman: 0.067