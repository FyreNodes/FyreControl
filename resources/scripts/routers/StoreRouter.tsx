import React from 'react';
import { NavLink, Route, RouteComponentProps, Switch } from 'react-router-dom';
import NavigationBar from '@/components/NavigationBar';
import NotFound from '@/components/elements/ScreenBlock';
import TransitionRouter from '@/TransitionRouter';
import SubNavigation from '@/components/elements/SubNavigation';
import StoreContainer from '@/components/dashboard/store/StoreContainer';
import ConfigurePlan from '@/components/dashboard/store/ConfigurePlan';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faStore } from '@fortawesome/free-solid-svg-icons';
import SubscriptionFailContainer from '@/components/dashboard/store/SubscriptionFailContainer';
import {useLocation} from "react-router";

export default () => {
    const location = useLocation();

    return (
        <>
            <NavigationBar/>
            {location.pathname.startsWith('/store') &&
                <SubNavigation>
                    <div>
                        <NavLink to={'/store'} exact><FontAwesomeIcon icon={faStore}/> Plans</NavLink>
                    </div>
                </SubNavigation>
            }
            <TransitionRouter>
                <Switch location={location}>
                    <Route path={'/store'} exact>
                        <StoreContainer/>
                    </Route>
                    <Route path={'/store/configure/:id'} exact>
                        <ConfigurePlan/>
                    </Route>
                    <Route path={'/fail'} exact>
                        <SubscriptionFailContainer/>
                    </Route>
                    <Route path={'*'} component={NotFound}/>
                </Switch>
            </TransitionRouter>
        </>
    )
}
