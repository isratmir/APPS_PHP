'use strict'

window.emiter = new EventEmitter();

var Form = React.createClass({
    onClickHandler: function (e) {
        e.preventDefault();
        var numbers = ReactDOM.findDOMNode(this.refs.ticketNumbers);
        var tickets = [];

        $.post('/lottery/search',{
             numbers: numbers.value
            }, function (response) {
                tickets = response;
                window.emiter.emit('Ticket.get', tickets);
            }
        );
    },
    render: function () {
        return (
            <div className="col-xs-12 col-sm-6 col-sm-offset-3">
                <div className="search-form">
                    <form action="/search" method="post">
                        <div className="form-group">
                            <input defaultValue='' ref='ticketNumbers' type="text" name="number" className="form-control" />
                                <p className="help-block">
                                    Введите номер билета. Если их более чем один вводите через запятую.
                                </p>
                        </div>
                        <input type="submit" value="Искать" onClick={this.onClickHandler} className="btn btn-primary btn-block" />
                    </form>
                </div>
            </div>
        )
    }
});

var Prize = React.createClass({
    render: function () {
        var date = this.props.data[0];
        var ticket = this.props.data[1];
        var prize = this.props.data[2];
        return (
            <div className="col-xs-12 col-sm-6 col-sm-offset-3">
                <div className="prize ">
                    <p className="bg bg-success">{date}: {ticket} - {prize}</p>
                </div>
            </div>
        )
    }
});

var Prizes = React.createClass({
    render: function () {
        var data = this.props.data;
        var template = data.map(function(item, index){
            return(
                <div key={index}>
                    <Prize data={item} />
                </div>
            )
        });
        return(
            <div>{template}</div>
        )
    }
});

var App = React.createClass({
    getInitialState: function () {
      return{
          prize: []
      }
    },
    componentDidMount: function () {
      var self = this;
      window.emiter.addListener('Ticket.get', function (tickets) {
          self.setState({prize: tickets});
      });
    },
    render: function () {
        return (
            <div>
                <Form />
                <Prizes data={this.state.prize} />
            </div>
        )
    }
});

ReactDOM.render(
    <App />,
    document.getElementById('app')
);
