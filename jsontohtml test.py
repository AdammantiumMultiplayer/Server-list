import urllib.request
import requests
import urllib
from urllib.request import urlopen
import urllib3

file_url = 'https://bns.devforce.de/bns.txt'

for line in urllib.request.urlopen(file_url):
    print(line.decode('utf-8')) 