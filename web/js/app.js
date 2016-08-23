var Website = React.createClass({
    render: function () {
        return(
            <div className="website">
                <ul className="list-unstyled">
                    <li>
                        <h4>
                            <img src={"https://www.google.com/s2/favicons?domain=" + this.props.website.url} alt=""/>&nbsp;
                            {this.props.website.title}
                        </h4>
                    </li>
                    <li><a href={this.props.website.url} target="_blank">{this.props.website.url}</a></li>
                </ul>
                <p>
                    {this.props.website.description}
                </p>
            </div>
        )
    }
});

var App = React.createClass({
    getInitialState: function () {
      return { website: '' };
    },
    componentDidMount: function () {
      this.getWebsite();
    },
    getWebsite: function () {
      $.get("/ajax/website", function (data) {
          var site = data[0];
          this.setState({website: site});
      }.bind(this));
    },
    render: function () {
        return(
            <div className="app">
                <Website website={this.state.website}/>
                <button className="btn btn-block btn-success" onClick={this.getWebsite}>Еще</button>
            </div>
        )
    }
});

ReactDOM.render(
    <App />,
    document.getElementById('app')
);
