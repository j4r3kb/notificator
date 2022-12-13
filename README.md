* [Requirements](#requirements)
* [Decisions](#decisions)
* [Usage](#usage)

# Requirements
Create a service that accepts the necessary information and sends a notification to customers.
It should provide an abstraction between at least two different messaging service providers.
It can use different messaging services/technologies for communication
(e.g. SMS, email, push notification, Facebook Messenger etc). If one of the services goes down,
your service can quickly failover to a different provider without affecting your customers.
Example messaging providers:
* Emails: AWS SES (https://docs.aws.amazon.com/ses/latest/APIReference/API_SendEmail.html)
* SMS messages: Twilio (https://www.twilio.com/docs/sms/api)
* Push notifications: Pushy (https://pushy.me/docs/api/send-notifications)

All listed services are free to try and are pretty painless to sign up for, so please register your own
test accounts on each. Here is what we want to see in the service:
* Multi-channel: service can send messages via the multiple channels, with a fail-over
* Configuration-driven: It is possible to enable / disable different communication channels with configuration.
* (Bonus point) Localisation: service supports localised messages, in order for the customer to receive communication 
in their preferred language.
* (Bonus point) Usage tracking: we can track what messages were sent, when and to whom.

# Decisions
* I have skipped API authentication and authorization.
* I have skipped more sophisticated exception handling in the API (what could be displayed and what should not).
* I have skipped API doc (f.e. Swagger)
* I haven't implemented any of the third-party services as I assume you are more interested to see how I would
lay out the application rather than to see if I can use Guzzle to handle external API. However, all core behavior
is covered by tests and Fake classes.
* There is no query bus.
* In real application the `Recipient` probably could have multiple addresses per `Channel` and one of them chosen as
preferred. That would make choosing the correct `Channel` more complex than it is in this example.
* For the localisation/translation I have created only `TranslatorInterface`. If the messages contain any
placeholder values then the translation gets more complex too. Also, I don't believe that there are any automated
translation services of high quality which anyone would use to translate important business content to send to customers.
More probably the content of `Notification` would be provided with multiple language versions in place.
* For usage tracking I have used `LoggerInterface` only. This would have to be changed for some custom
logging service that could write data to a database for more flexible analysis.
* Configuration - implementations of `NotificationChannelInterface` are loaded to the pool via Symfony's ComplierPass.
In real application in order to be able to on/off any of the services without changing any (config) files,
configuration had to be kept in a database. As for circuit breaker the method `NotificationChannelInterface::isAvailable`
could for example use https://github.com/ackintosh/ganesha.

# Usage
Please use `app` script in the root directory:
* `./app start`
* `./app composer install`
* `./app console doctrine:schema:create`
* `./app console doctrine:schema:create --env=test`
* `./app phpunit`
* To call the API use `http://localhost:8888/notification` see [config/routes.yaml](config/routes.yaml)
* `./app stop`