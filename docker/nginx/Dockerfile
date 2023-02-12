FROM nginx:latest

COPY core/default.conf.template /etc/nginx/conf.d/default.conf.template

# Entrypoint
ADD bin/docker-entrypoint-nginx.sh /usr/local/bin/
RUN chmod 777 /usr/local/bin/docker-entrypoint-nginx.sh \
    && ln -s /usr/local/bin/docker-entrypoint-nginx.sh /

ENTRYPOINT ["docker-entrypoint-nginx.sh"]

CMD ["nginx", "-g", "daemon off;"]
