# Amazon CloudWatch

## Elasticsearch

```
-
    id: es
    provider: \StpBoard\Amazon\CloudWatch\Elasticsearch\ControllerProvider
    width: 4
    refresh: 3600
    params:
        name: NAME
        action: ACTION
        region: REGION
        aws_key: AWS_KEY
        aws_secret: AWS_SECRET
        domain_name: ES_DOMAIN_NAME
        client_id: AWS_CLIENT_ID
```

Available `ACTION`:

* count - document count from last month

Parameter `NAME` - name which will be displayed on chart

Parameter `REGION` - like 'eu-west-1` etc.

Parameter `AWS_KEY` - aws key

Parameter `AWS_SECRET` - aws secret

Parameter `ES_DOMAIN_NAME` - Elasticsearch domain name

Parameter `AWS_CLIENT_ID` - Amazon account client id
