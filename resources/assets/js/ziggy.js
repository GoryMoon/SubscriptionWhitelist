    var Ziggy = {
        namedRoutes: {"subscriber":{"uri":"subscriber","methods":["GET","HEAD"],"domain":null},"subscriber.save":{"uri":"subscriber\/{channel}","methods":["PUT"],"domain":null},"broadcaster":{"uri":"broadcaster","methods":["GET","HEAD"],"domain":null},"broadcaster.list":{"uri":"broadcaster\/list","methods":["GET","HEAD"],"domain":null},"broadcaster.data":{"uri":"broadcaster\/list\/data","methods":["GET","HEAD"],"domain":null},"broadcaster.list_stats":{"uri":"broadcaster\/list\/stats","methods":["GET","HEAD"],"domain":null},"broadcaster.list.add":{"uri":"broadcaster\/list\/add","methods":["POST"],"domain":null},"broadcaster.sync":{"uri":"broadcaster\/list\/sync","methods":["POST"],"domain":null},"broadcaster.invalid":{"uri":"broadcaster\/list\/invalid","methods":["DELETE"],"domain":null},"broadcaster.delete":{"uri":"broadcaster\/list\/all","methods":["DELETE"],"domain":null},"broadcaster.delete_entry":{"uri":"broadcaster\/list\/{id}","methods":["DELETE"],"domain":null},"broadcaster.stats":{"uri":"broadcaster\/stats","methods":["GET","HEAD"],"domain":null}},
        baseUrl: 'https://whitelist.test/',
        baseProtocol: 'https',
        baseDomain: 'whitelist.test',
        basePort: false,
        defaultParameters: []
    };

    if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
        for (var name in window.Ziggy.namedRoutes) {
            Ziggy.namedRoutes[name] = window.Ziggy.namedRoutes[name];
        }
    }

    export {
        Ziggy
    }
