FROM armhf/alpine:3.5

RUN apk add --no-cache python2 py2-pip py-mysqldb

RUN pip install xbee

RUN pip install arrow

ADD redqueen.py /

ENTRYPOINT [ "python", "/redqueen.py" ]
