ARG STACK_VERSION

FROM docker.elastic.co/elasticsearch/elasticsearch:${STACK_VERSION}

WORKDIR /hostfs

COPY .docker/elasticsearch/docker-certs-entrypoint.sh /usr/local/bin/docker-certs-entrypoint.sh
# RUN chmod +x /usr/local/bin/docker-certs-entrypoint.sh

# CMD ["bash", "-c", "cat /usr/local/bin/docker-certs-entrypoint.sh"]

# VOLUME /.elasticsearch
# COPY /usr/share/elasticsearch/config/certs/ca/ca.crt .