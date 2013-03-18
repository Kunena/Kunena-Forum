/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
window.addEvent('domready', function () {
    var rate = function (star) {
        var form = document.getElementById('krate-form');
        var url = form
            .getProperty('action');
        var token = form.getChildren('input')[0].name;
        var id = document.getElementsByName('id')[0].value;
        var catid = document.getElementsByName('catid')[0].value;
        url = url + '&' + token + '=1&view=topic&task=rate&id=' + id + '&catid=' + catid + '&stars=' + star + '&type=ajax';
        var res = new Request({
            method:'post',
            url:url,
            data:{'view':'topic',
                'task':'rate',
                'id':id,
                'catid':catid,
                'stars':star,
                'type':'ajax'},
            onComplete:function (r) {
                document.getElementsByName('krating')[(5-r)].setProperty('checked', 'checked');
            }
        }).send();
    }

    document.getElementById("star1")
        .addEvent('click', function (e) {
            e.stop();
            rate(1);
        });
    document.getElementById("star2")
        .addEvent('click', function (e) {
            e.stop();
            rate(2);
        });
    document.getElementById("star3")
        .addEvent('click', function (e) {
            e.stop();
            rate(3);
        });
    document.getElementById("star4")
        .addEvent('click', function (e) {
            e.stop();
            rate(4);
        });
    document.getElementById("star5")
        .addEvent('click', function (e) {
            e.stop();
            rate(5);
        });
})