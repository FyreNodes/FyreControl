import React from 'react';
import { NavLink, Route, RouteComponentProps, Switch } from 'react-router-dom';
import NavigationBar from '@/components/NavigationBar';
import NotFound from '@/components/elements/ScreenBlock';
import TransitionRouter from '@/TransitionRouter';
import SubNavigation from '@/components/elements/SubNavigation';
import StaffRequestsContainer from '@/components/dashboard/staff/StaffRequestsContainer';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faPlus } from '@fortawesome/free-solid-svg-icons';
import {useLocation} from "react-router";

export default () => {
    const location = useLocation();

    return (
        <>
            <NavigationBar/>
            {location.pathname.startsWith('/staff') &&
                <SubNavigation>
                    <div>
                        <NavLink to={'/staff'} exact><FontAwesomeIcon icon={faPlus}/> Request</NavLink>
                    </div>
                </SubNavigation>
            }
            <TransitionRouter>
                <Switch location={location}>
                    <Route path={'/staff'} exact>
                        <StaffRequestsContainer/>
                    </Route>
                    <Route path={'*'} component={NotFound}/>
                </Switch>
            </TransitionRouter>
        </>
    )
};
