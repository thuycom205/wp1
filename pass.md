jane

john@doe9988
 git checkout tags/2.2.0


php -f /home/thuy/work/setup/sampledata/magento2-sample-data/dev/tools/build-sample-data.php -- --ce-source=/var/www/html/magento/ee2


sudo vim /etc/apache2/sites-enabled/000-default.conf


sudo su - postgres -c "createuser -s $USER"
https://stackoverflow.com/questions/5178416/pip-install-lxml-error
sudo apt-get install libxml2-dev libxslt-dev python-dev
sudo apt-get install ldap
sudo apt-get install ldap-dev

sudo apt-get install libsasl2-dev python-dev libldap2-dev libssl-dev

For libraries using native code (Pillow, lxml, greenlet, gevent, psycopg2, ldap) it may be necessary to install development tools and native dependencies before pip is able to install the dependencies themselves. These are available in -dev or -devel packages for Python, Postgres, libxml2, libxslt, libevent, libsasl2 and libldap2. Then the Python dependecies can themselves be installed


Marko hidden  model


apt-get install -y npm


sudo ln -s /usr/bin/nodejs /usr/bin/node


http://linuxbsdos.com/2017/06/11/how-to-install-nvidia-geforce-gtx-1070-drivers-on-ubuntu-16-10-17-04/


https://github.com/odoo/odoo/issues/16451
 ./odoo-bin --addons-path=/home/thuy/work/setup/odoo/odoo/addons
 
 python  ./odoo-bin --addons-path=/home/thuy/work/setup/odoo/odoo/addons

 
 For example, the hidden state of “Sunny” might emit high temperature readings, but
 occasionally also low readings for one reason or another.
 In a HMM, we have to define the emission probability, which is usually represented as a
 matrix called the emission matrix. The number of rows of the matrix is the number of states
 (Sunny, Cloudy, Rainy), and the number of columns is the number of different types of
 observations (Hot, Mild, Cold). Each element of the matrix is the probability associated with
 the emission.
 The canonical way of visualizing a HMM is by appending the trellis with observations, as
 shown in figure 6.5.
 
 
virtualenv --system-site-packages ~/tensorflow


stat -c "%a" '/usr/local/lib/python3.5/dist-packages/protobuf-3.1.0.post1-py3.5.egg/EGG-INFO/namespace_packages.txt'
640
stat -c "%a" '/usr/local/lib/python3.5'
thuy@thuy-XPS-15-9550:~/work/setup/PhpStorm-173.3942.32/bin$ stat -c "%a" '/usr/local/lib/python3.5'
2775
obtain in an efficient way. We'll see how to invoke those tools in this chapter when we star using our very first neural 
network


architect called 

TensorFlow comes with many helper functions to help you obtain the parameters of a neural
network in an efficient way. We’ll see how to invoke those tools in this chapter when we start
using our very first neural network architecture called autoencoder
But an autoencoder is more interesting than that. It contains a small hidden layer! If that
hidden layer has a smaller dimension than the input, the hidden layer is a compression of your

exaggerated 
exaggerated

of 
hidden layer 

encoder
decoder

data, called encoding. The process of reconstructing the input from the hidden layer is called
decoding. Figure 7.8 shows an exaggerated example of an autoencoder.
Encoding is a great way to reduce the dimension of the input. For example, if we can
represent a 256 by 256 image in just 100 hidden nodes, then we’ve reduced each data item
by a factor of hundreds!
EXERCISE 7.2 Let x denote the input-vector (x1, x2, ..., xn), and let y denote the output-vector (y1, y2, ...,
yn). Lastly, let w and w’ denote the encoder and decoder weights. What is a possible cost function to train this
neural network?
It makes sense to use an object-oriented programming style to implement an autoencoder.
That way, we can later reuse the class in other applications without worrying about tightly
coupled code. In fact, creating our code as outlined in listing 7.1 helps build deeper
architectures, such as a stacked autoencoder, which has been known to perform better
empirically.
TIP Generally with neural networks, adding m


class Autoencoder:
def __init__(self, input_dim, hidden_dim, epoch=250, learning_rate=0.001):
self.epoch = epoch ❶
self.learning_rate = learning_rate ❷
x = tf.placeholder(dtype=tf.float32, shape=[None, input_dim]) ❸
with tf.name_scope('encode'):
❹
weights = tf.Variable(tf.random_normal([input_dim, hidden_dim],
dtype=tf.float32), name='weights')
biases = tf.Variable(tf.zeros([hidden_dim]), name='biases')
encoded = tf.nn.tanh(tf.matmul(x, weights) + biases)
with tf.name_scope('decode'):
❺
weights = tf.Variable(tf.random_normal([hidden_dim, input_dim],
dtype=tf.float32), name='weights')
biases = tf.Variable(tf.zeros([input_dim]), name='biases')
decoded = tf.matmul(encoded, weights) + biases
self.x = x
self.encoded = encoded
self.decoded = decoded
❻
❻
❻
self.loss = tf.sqrt(tf.reduce_mean(tf.square(tf.subtract(self.x, self.decoded))))
❼
self.train_op = tf.train.RMSPro

https://github.com/tensorflow/tensorflow/issues/5343


https://developer.nvidia.com/cuda-downloads?target_os=Linux&target_arch=x86_64&target_distro=Ubuntu&target_version=1604&target_type=deblocal
https://askubuntu.com/questions/889015/cant-install-cuda-8-but-have-correct-nvidia-driver-ubuntu-16

https://medium.com/@acrosson/installing-nvidia-cuda-cudnn-tensorflow-and-keras-69bbf33dce8a
export PATH=/usr/local/cuda-9.1/bin:$PATH

https://github.com/tensorflow/tensorflow/issues/5343


https://devtalk.nvidia.com/default/topic/1024550/download-and-install-cuda-8-0-instead-of-cuda-9-0/


https://stackoverflow.com/questions/42217059/tensorflowattributeerror-module-object-has-no-attribute-mul


var width = window.innterWidth;
var height = window.innerHeight;

var tween = null;
bounceup into the air

simultaneous 
parallel
efficiently
efficiently

hieu qua
addStar(layer, stage) { 

var scale = Math.random();
var star = new Konvar.Start({});

}

x  : y
number 
fill
draggable : true
function addStar(layer, stage) {
}

laer .addstar
vart = New Konva.Sage();

dragLayer = new Konva.Layer();

stage.add();
stage .on ,
shar = ev.target.
shape.moveTo(dragLayer);

stage.draw();
of )_teem 
sta/seetStrrobi


vim /usr/local/zend/etc/php.ini


cp -r /home/thuy/work/setup/sampledata/magento2-sample-data/app/code/* /var/www/html/magento/ee2/app/code/

cp -r /home/thuy/work/setup/sampledata/magento2-sample-data/pub/media/* /var/www/html/magento/ee2/pub/media/

http://files.zend.com/help/Zend-Server/content/deb_installing_zend_server.htm
